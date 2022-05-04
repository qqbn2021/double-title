<?php

/**
 * 标题列表
 */
class Double_Title_List
{
    /**
     * 根据参数显示不同的页面
     * @return void
     */
    public static function home()
    {
        $action = '';
        if (!empty($_GET['action'])) {
            $action = sanitize_title($_GET['action']);
        }
        if ('sync_post' === $action) {
            // 同步文章
            self::sync_post();
        } else if ('sync_title' === $action) {
            // 同步标题
            self::sync_title();
        } else if ('delete' === $action) {
            // 删除标题
            self::delete_title();
        } else if ('edit' === $action) {
            // 编辑标题
            self::edit_title();
        } else {
            // 显示列表
            self::title_list();
        }
    }

    /**
     * 列表
     * @return void
     */
    public static function title_list()
    {
        ?>
        <div class="wrap">
            <h1 class="wp-heading-inline">双标题列表</h1>
            <form method="get">
                <input type="hidden" name="page" value="double-title-list"/>
                <?php
                $double_title_table = new Double_Title_Table();
                $double_title_table->prepare_items();
                $double_title_table->display();
                ?>
            </form>
        </div>
        <?php
    }

    /**
     * 同步文章
     * @return void
     */
    public static function sync_post()
    {
        $current_url = self_admin_url('admin.php?page=double-title-list');
        ?>
        <div class="wrap">
            <h1 class="wp-heading-inline">同步文章</h1>
            <p>将您发布的文章同步到双标题列表中，让之前的文章也可以使用双标题。</p>
            <p>
                <a class="button button-secondary" href="javascript:void(0);" id="sync_post">开始同步</a>
            </p>
            <a class="button button-primary" href="<?php echo esc_url($current_url); ?>">返回</a>
        </div>
        <?php
    }

    /**
     * 同步标题
     * @return void
     */
    public static function sync_title()
    {
        $current_url = self_admin_url('admin.php?page=double-title-list');
        ?>
        <div class="wrap">
            <h1 class="wp-heading-inline">同步标题</h1>
            <p>如果文章还没有设置副标题，同步标题将会自动从接口数据中随机取出一条数据作为副标题。</p>
            <p>
                <a class="button button-secondary" href="javascript:void(0);" id="sync_title">开始同步</a>
            </p>
            <a class="button button-primary" href="<?php echo esc_url($current_url); ?>">返回</a>
        </div>
        <?php
    }

    /**
     * 同步文章ajax处理
     * @return void
     */
    public static function wp_ajax_sync_post()
    {
        global $wpdb;
        global $double_title_option;
        $double_title_auto_title = 2;
        $double_title_template = '';
        if (!empty($double_title_option['double_title_auto_title'])) {
            $double_title_auto_title = $double_title_option['double_title_auto_title'];
        }
        if (!empty($double_title_option['double_title_template'])) {
            $double_title_template = $double_title_option['double_title_template'];
        }
        $table_name = $wpdb->prefix . 'double_titles';
        $post_table_name = $wpdb->prefix . 'posts';
        check_ajax_referer('double_title');
        $start_id = 0;
        $has_more = false;
        $limit = 1000;
        if (!empty($_POST['start_id'])) {
            $start_id = (int)$_POST['start_id'];
        }
        $start_id_tmp = $start_id;
        // 判断文章是否还有
        $total = $wpdb->get_var('SELECT COUNT(*) FROM `' . $post_table_name . '`');
        if ($start_id <= $total) {
            $has_more = true;
            // 查询此范围内已经同步过的文章
            $sync_post_ids = array();
            $sql = 'SELECT `post_id` FROM `' . $table_name . '` WHERE `id` > %d ORDER BY `id` ASC LIMIT %d';
            $query = $wpdb->prepare(
                $sql,
                $start_id,
                $limit
            );
            $results = $wpdb->get_results($query, 'ARRAY_A');
            if (!empty($results)) {
                if (function_exists('array_column')) {
                    $sync_post_ids = array_column($results, 'post_id');
                } else {
                    foreach ($results as $result) {
                        $sync_post_ids[] = $result['post_id'];
                    }
                }
            }
            if (empty($sync_post_ids)) {
                $sync_post_ids = array(0);
            }
            // 同步文章ID和标题
            $sql = 'SELECT `ID`,`post_title` FROM `' . $post_table_name . '` WHERE `ID` NOT IN (' . implode(', ', array_fill(0, count($sync_post_ids), '%d')) . ') AND `post_status` = "publish" AND `post_type` = "post" AND `ID` > %d ORDER BY `ID` ASC LIMIT %d';
            $query = call_user_func_array(array($wpdb, 'prepare'), array_merge(array($sql), $sync_post_ids, array($start_id, $limit)));
            $results = $wpdb->get_results($query, 'ARRAY_A');
            if (!empty($results)) {
                foreach ($results as $result) {
                    // 是否从原标题自动匹配副标题
                    $original_title = $result['post_title'];
                    $subtitle = '';
                    if (!empty($double_title_template) && $double_title_auto_title == 1) {
                        $auto_result = Double_Title_Plugin::get_auto_title($original_title, $double_title_template);
                        if (!empty($auto_result['original_title']) && !empty($auto_result['subtitle'])) {
                            $original_title = $auto_result['original_title'];
                            $subtitle = $auto_result['subtitle'];
                        }
                    }
                    $start_id = $result['ID'];
                    $sql = 'INSERT INTO `' . $table_name . '`(`post_id`,`original_title`,`subtitle`) VALUES (%d,%s,%s)';
                    $query = $wpdb->prepare(
                        $sql,
                        $result['ID'],
                        $original_title,
                        $subtitle
                    );
                    $wpdb->query($query);
                }
            }
        }
        if ($start_id_tmp === $start_id) {
            $start_id += $limit;
        }
        wp_send_json(array(
            'start_id' => $start_id,
            'has_more' => $has_more
        ));
    }

    /**
     * 同步标题
     * @return void
     */
    public static function wp_ajax_sync_title()
    {
        global $wpdb;
        global $double_title_option;
        $double_title_auto_title = 2;
        $double_title_template = '';
        if (!empty($double_title_option['double_title_auto_title'])) {
            $double_title_auto_title = $double_title_option['double_title_auto_title'];
        }
        if (!empty($double_title_option['double_title_template'])) {
            $double_title_template = $double_title_option['double_title_template'];
        }
        $table_name = $wpdb->prefix . 'double_titles';
        check_ajax_referer('double_title');
        $start_id = 0;
        $has_more = false;
        if (!empty($_POST['start_id'])) {
            $start_id = (int)$_POST['start_id'];
        }
        $sql = 'SELECT `id`,`original_title` FROM `' . $table_name . '` WHERE `id` > %d AND `subtitle` = "" ORDER BY `id` ASC LIMIT 1';
        $query = $wpdb->prepare(
            $sql,
            $start_id
        );
        $results = $wpdb->get_results($query, 'ARRAY_A');
        $msg = '';
        $subtitle = '';
        $auth_fail = false;
        if (!empty($results[0])) {
            $has_more = true;
            $start_id = $results[0]['id'];
            $original_title = $results[0]['original_title'];
            // 是否从原标题自动匹配副标题
            if (!empty($double_title_template) && $double_title_auto_title == 1) {
                $auto_result = Double_Title_Plugin::get_auto_title($original_title, $double_title_template);
                if (!empty($auto_result['original_title']) && !empty($auto_result['subtitle'])) {
                    $original_title = $auto_result['original_title'];
                    $subtitle = $auto_result['subtitle'];
                }
            }
            if (empty($subtitle)) {
                $subtitle = self::get_subtitle($original_title);
            }
            if (!empty($subtitle)) {
                $sql = 'UPDATE `' . $table_name . '` SET `original_title` = %s,`subtitle` = %s WHERE `id` = %d';
                $query = $wpdb->prepare(
                    $sql,
                    $original_title,
                    $subtitle,
                    $start_id
                );
                $wpdb->query($query);
            } else {
                $double_title_error = get_transient('double_title_error');
                if (!empty($double_title_error)) {
                    $msg = $double_title_error;
                    $has_more = false;
                    $auth_fail = true;
                } else {
                    $msg = '获取副标题失败';
                }
            }
        }
        wp_send_json(array(
            'start_id' => $start_id,
            'has_more' => $has_more,
            'subtitle' => $subtitle,
            'auth_fail' => $auth_fail,
            'msg' => $msg
        ));
    }

    /**
     * 修改副标题
     * @return void
     */
    public static function wp_ajax_update_title()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'double_titles';
        check_ajax_referer('double_title');
        $result = false;
        $id = 0;
        if (!empty($_POST['id'])) {
            $id = (int)$_POST['id'];
        }
        $subtitle = '';
        if (!empty($_POST['subtitle'])) {
            $subtitle = sanitize_text_field($_POST['subtitle']);
        }
        if (!empty($subtitle) && !empty($id)) {
            $sql = 'UPDATE `' . $table_name . '` SET `subtitle` = %s WHERE `id` = %d';
            $query = $wpdb->prepare(
                $sql,
                $subtitle,
                $id
            );
            $result = $wpdb->query($query);
        }
        wp_send_json(array(
            'result' => $result,
        ));
    }

    /**
     * 根据原标题获取副标题
     * @param string $original_title 原标题
     * @return array
     */
    public static function get_subtitles($original_title)
    {
        global $double_title_option;
        delete_transient('double_title_error');
        $options = $double_title_option;
        if (!empty($options['double_title_domain'])) {
            $domain = $options['double_title_domain'];
        } else {
            $domain = parse_url(get_home_url(), PHP_URL_HOST);
        }
        if (!empty($options['double_title_cdkey'])) {
            $cdkey = $options['double_title_cdkey'];
        } else {
            set_transient('double_title_error', '未设置授权码', 60);
            return array();
        }
        $api_url = 'https://dev.ggdoc.cn/api/double_title/get';
        $timeout = 30;
        if (!empty($options['double_title_timeout'])) {
            $timeout = (int)$options['double_title_timeout'];
        }
        $response = wp_remote_post($api_url, array(
            'timeout' => $timeout,
            'headers' => array(
                'GGDEV-CDKEY' => $cdkey,
                'GGDEV-ACTIVATE-DOMAIN' => $domain
            ),
            'body' => array(
                'title' => $original_title
            )
        ));
        if (is_wp_error($response)) {
            set_transient('double_title_error', $response->get_error_message(), 60);
            return array();
        }
        $code = (int)wp_remote_retrieve_response_code($response);
        if ($code !== 200) {
            set_transient('double_title_error', '接口返回状态码为：' . $code, 60);
            return array();
        }
        $result = wp_remote_retrieve_body($response);
        if (empty($result)) {
            set_transient('double_title_error', '接口返回数据为空', 60);
            return array();
        }
        $result_arr = json_decode($result, true);
        if (empty($result_arr)) {
            set_transient('double_title_error', '接口返回数据解析失败', 60);
            return array();
        }
        if ($result_arr['status'] != 1) {
            set_transient('double_title_error', $result_arr['msg'], 60);
            return array();
        }
        // 根据相似度排序
        if (!empty($result_arr['data']) && count($result_arr['data']) > 1) {
            $result_arr['data'] = array_diff($result_arr['data'], array($original_title));
            $data = array();
            $similars = array();
            foreach ($result_arr['data'] as $k => $v) {
                $similar = similar_text($original_title, $v);
                $data[$k] = array(
                    'value' => $v,
                    'similar' => $similar
                );
                $similars[$k] = $similar;
            }
            if (array_multisort($similars, SORT_ASC, $data)) {
                if (function_exists('array_column')) {
                    return array_column($data, 'value');
                } else {
                    $tmp = array();
                    foreach ($data as $v) {
                        $tmp[] = $v['value'];
                    }
                    return $tmp;
                }
            }
        }
        return $result_arr['data'];
    }

    /**
     * 获取一个副标题
     * @param string $original_title
     * @return mixed|string
     */
    public static function get_subtitle($original_title)
    {
        global $double_title_option;
        $subtitles = self::get_subtitles($original_title);
        if (empty($subtitles)) {
            return '';
        }
        $double_title_similar = 1;
        if (!empty($double_title_option['double_title_similar'])) {
            $double_title_similar = $double_title_option['double_title_similar'];
        }
        if ($double_title_similar == 1) {
            $subtitles = array_reverse($subtitles);
        } else if ($double_title_similar == 2) {
            $len = count($subtitles);
            if ($len > 1) {
                $i = (int)($len / 2);
                $subtitles[0] = $subtitles[$i];
            }
        }
        return $subtitles[0];
    }

    /**
     * 删除标题
     * @return void
     */
    public static function delete_title()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'double_titles';
        $current_url = self_admin_url('admin.php?page=double-title-list');
        $result = false;
        if (!empty($_GET['_wpnonce']) && wp_verify_nonce($_GET['_wpnonce'], 'bulk-double_titles')) {
            $query = '';
            if (!empty($_GET['id'])) {
                // 删除单条记录
                $id = (int)$_GET['id'];
                $sql = 'DELETE FROM `' . $table_name . '` WHERE `id` = %d';
                $query = $wpdb->prepare(
                    $sql,
                    $id
                );
            } else if (!empty($_GET['ids']) && is_array($_GET['ids'])) {
                // 删除多条记录
                $ids = array_map('absint', rest_sanitize_array(wp_unslash($_GET['ids'])));
                if (!empty($ids)) {
                    $sql = 'DELETE FROM `' . $table_name . '` WHERE `id` in (' . implode(', ', array_fill(0, count($ids), '%d')) . ')';
                    $query = call_user_func_array(array($wpdb, 'prepare'), array_merge(array($sql), $ids));
                }
            }
            if (!empty($query)) {
                $result = $wpdb->query($query);
            }
        }
        ?>
        <div class="wrap">
            <h1 class="wp-heading-inline">删除双标题</h1>
            <p>
                <?php
                if ($result !== false) {
                    ?>
                    删除<?php echo esc_html($result); ?>条记录成功
                    <?php
                } else {
                    ?>
                    删除失败
                    <?php
                }
                ?>
            </p>
            <a class="button button-primary" href="<?php echo esc_url($current_url); ?>">返回</a>
        </div>
        <?php
    }

    /**
     * 编辑标题
     * @return void
     */
    public static function edit_title()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'double_titles';
        $current_url = self_admin_url('admin.php?page=double-title-list');
        $post_id = 0;
        $id = 0;
        $original_title = '';
        $subtitle = '';
        $subtitles = array();
        if (!empty($_GET['id'])) {
            $id = (int)$_GET['id'];
            $sql = 'SELECT `post_id`,`original_title`,`subtitle` FROM `' . $table_name . '` WHERE `id` = %d';
            $query = $wpdb->prepare(
                $sql,
                $id
            );
            $results = $wpdb->get_results($query, 'ARRAY_A');
            if (!empty($results[0])) {
                $post_id = $results[0]['post_id'];
                $original_title = $results[0]['original_title'];
                $subtitle = $results[0]['subtitle'];
                $subtitles = self::get_subtitles($original_title);
            }
        }
        ?>
        <div class="wrap">
            <h1 class="wp-heading-inline">编辑双标题</h1>
            <p>
                文章ID：<?php echo esc_html($post_id); ?>
            </p>
            <p>
                原标题：<?php echo esc_html($original_title); ?>
            </p>
            <p>
                副标题：<?php echo esc_html($subtitle); ?>
            </p>
            <p>
                <?php
                if (!empty($subtitles)) {
                    ?>
                    <input type="hidden" id="double_title_id" name="double_title_id"
                           value="<?php echo esc_attr($id); ?>"/>
                    <select name="double_title_subtitle" id="double_title_subtitle">
                        <option value="">选择副标题</option>
                        <?php
                        foreach ($subtitles as $v) {
                            ?>
                            <option value="<?php echo esc_attr($v); ?>"><?php echo esc_html($v); ?></option>
                            <?php
                        }
                        ?>
                    </select>
                    <?php
                } else {
                    $double_title_error = get_transient('double_title_error');
                    if (empty($double_title_error)) {
                        $double_title_error = '获取副标题失败';
                    }
                    echo esc_html($double_title_error);
                }
                ?>
            </p>
            <a class="button button-primary" href="<?php echo esc_url($current_url); ?>">返回</a>
        </div>
        <?php
    }
}