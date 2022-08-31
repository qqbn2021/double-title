<?php

/**
 * 基础类
 */
class Double_Title_Plugin
{
    // 启用插件
    public static function plugin_activation()
    {
        // 创建默认配置
        add_option('double_title_options', array(
            'timeout' => 30,
            'template' => '{原标题}（{副标题}）',
            'priority' => 10,
            'similar' => 1,
            'len' => 0,
            'wp_title_parts' => 2,
            'tab_title' => 1,
            'pre_generate' => 2,
            'article_title' => 2
        ));
    }

    // 删除插件执行的代码
    public static function plugin_uninstall()
    {
        // 删除元数据
        delete_metadata('post', 0, 'double_title_original_title', '', true);
        delete_metadata('post', 0, 'double_title_subtitle', '', true);
        // 删除配置
        delete_option('double_title_options');
    }

    /**
     * 添加设置链接
     * @param array $links
     * @return array
     */
    public static function link_double_title($links)
    {
        $business_link = '<a href="https://www.ggdoc.cn/plugin/2.html" target="_blank">商业版</a>';
        array_unshift($links, $business_link);

        $settings_link = '<a href="options-general.php?page=double-title-setting">设置</a>';
        array_unshift($links, $settings_link);

        return $links;
    }

    /**
     * 表单输入框回调
     *
     * @param array $args 这数据就是add_settings_field方法中第6个参数（$args）的数据
     */
    public static function double_title_field_callback($args)
    {
        global $double_title_options;
        // 表单的id或name字段
        $id = $args['label_for'];
        // 表单的类型
        $form_type = isset($args['form_type']) ? $args['form_type'] : 'input';
        // 输入表单说明
        $form_desc = isset($args['form_desc']) ? $args['form_desc'] : '';
        // 输入表单type
        $type = isset($args['type']) ? $args['type'] : 'text';
        // 输入表单placeholder
        $form_placeholder = isset($args['form_placeholder']) ? $args['form_placeholder'] : '';
        // 下拉框等选项值
        $form_data = isset($args['form_data']) ? $args['form_data'] : array();
        // 表单的名称
        $input_name = 'double_title_options[' . $id . ']';
        // 获取表单选项中的值
        $options = $double_title_options;
        // 表单的值
        $input_value = isset($options[$id]) ? $options[$id] : '';
        switch ($form_type) {
            case 'input':
                ?>
                <input id="<?php echo esc_attr($id); ?>" type="<?php echo esc_attr($type); ?>"
                       placeholder="<?php echo esc_attr($form_placeholder); ?>"
                       name="<?php echo esc_attr($input_name); ?>" value="<?php echo esc_attr($input_value); ?>"
                       class="regular-text">
                <?php
                break;
            case 'select':
                ?>
                <select id="<?php echo esc_attr($id); ?>" name="<?php echo esc_attr($input_name); ?>">
                    <?php
                    foreach ($form_data as $v) {
                        $selected = '';
                        if ($v['value'] == $input_value) {
                            $selected = 'selected';
                        }
                        ?>
                        <option <?php selected($selected, 'selected'); ?>
                                value="<?php echo esc_attr($v['value']); ?>"><?php echo esc_html($v['title']); ?></option>
                        <?php
                    }
                    ?>
                </select>
                <?php
                break;
            case 'checkbox':
                ?>
                <fieldset><p>
                        <?php
                        $len = count($form_data);
                        foreach ($form_data as $k => $v) {
                            $checked = '';
                            if (!empty($input_value) && in_array($v['value'], $input_value)) {
                                $checked = 'checked';
                            }
                            ?>
                            <label>
                                <input type="checkbox" value="<?php echo esc_attr($v['value']); ?>"
                                       id="<?php echo esc_attr($id . '_' . $v['value']); ?>"
                                       name="<?php echo esc_attr($input_name . '[]'); ?>"
                                    <?php checked($checked, 'checked'); ?>><?php echo esc_html($v['title']); ?>
                            </label>
                            <?php
                            if ($k < ($len - 1)) {
                                ?>
                                <br>
                                <?php
                            }
                        }
                        ?>
                    </p></fieldset>
                <?php
                break;
            case 'textarea':
                ?>
                <textarea id="<?php echo esc_attr($id); ?>"
                          placeholder="<?php echo esc_attr($form_placeholder); ?>"
                          name="<?php echo esc_attr($input_name); ?>" class="large-text code"
                          rows="5"><?php echo esc_attr($input_value); ?></textarea>
                <?php
                break;
        }
        if (!empty($form_desc)) {
            ?>
            <p class="description"><?php echo esc_html($form_desc); ?></p>
            <?php
        }
    }

    // 初始化
    public static function admin_init()
    {
        // 注册设置页面
        Double_Title_Page::init_page();
    }

    // 添加菜单
    public static function admin_menu()
    {
        add_options_page(
            '双标题',
            '双标题',
            'manage_options',
            'double-title-setting',
            array('Double_Title_Plugin', 'show_page')
        );
    }

    // 显示设置页面
    public static function show_page()
    {
        // 检查用户权限
        if (!current_user_can('manage_options')) {
            return;
        }
        ?>
        <div class="wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            <form action="options.php" method="post">
                <?php
                // 输出表单
                settings_fields('double_title_page');
                do_settings_sections('double_title_page');
                // 输出保存设置按钮
                submit_button('保存更改');
                ?>
            </form>
        </div>
        <?php
    }

    /**
     * 修改标题
     * @param string $title 标题
     * @param int $id 文章ID
     * @return string
     */
    public static function change_title($title, $id)
    {
        if (self::check_term_ids()) {
            if (get_post_type($id) === 'post') {
                $tmp = self::get_double_title($id, $title);
                if (!empty($tmp)) {
                    $title = $tmp;
                }
            }
        }
        return $title;
    }

    /**
     * wp_title_parts过滤器
     * @param array $title_array
     * @return array
     */
    public static function wp_title_parts($title_array)
    {
        if (!is_home() && self::check_term_ids() && !empty($title_array)) {
            $id = get_the_ID();
            if (!empty($id) && get_post_type($id) === 'post') {
                if (1 == count($title_array)) {
                    $tmp = self::get_double_title($id, $title_array[0]);
                    if (!empty($tmp)) {
                        $title_array[0] = $tmp;
                    }
                } else {
                    $blog_name = get_bloginfo('name');
                    foreach ($title_array as $k => $title) {
                        if ($title === $blog_name) {
                            continue;
                        }
                        $tmp = self::get_double_title($id, $title);
                        if (!empty($tmp)) {
                            $title_array[$k] = $tmp;
                        }
                    }
                }
            }
        }
        return $title_array;
    }

    /**
     * 自动提取副标题
     * @param string $original_title 原标题
     * @param string $double_title_template 双标题模板
     * @return array
     */
    public static function get_auto_title($original_title, $double_title_template)
    {
        $subtitle = '';
        if (!empty($original_title) && !empty($double_title_template)) {
            $double_title_template = str_replace('｛', '{', $double_title_template);
            $double_title_template = str_replace('｝', '}', $double_title_template);
            $double_title_template = preg_quote($double_title_template, '/');
            $double_title_template = str_replace('\{原标题\}', '(?P<original_title>.*)', $double_title_template);
            $double_title_template = str_replace('\{副标题\}', '(?P<subtitle>.*)', $double_title_template);
            if (preg_match('/' . $double_title_template . '/iu', $original_title, $mat)) {
                $original_title = $mat['original_title'];
                $subtitle = $mat['subtitle'];
            }
        }
        return array(
            'original_title' => $original_title,
            'subtitle' => $subtitle
        );
    }

    /**
     * 修改head标签中的标题
     * @param array $title_parts_array
     * @return array
     */
    public static function change_document_title($title_parts_array)
    {
        if (!is_home() && self::check_term_ids()) {
            $id = get_the_ID();
            if (!empty($id) && get_post_type($id) === 'post') {
                $title = self::get_double_title($id, $title_parts_array['title']);
                if (!empty($title)) {
                    $title_parts_array['title'] = $title;
                }
            }
        }
        return $title_parts_array;
    }

    /**
     * 检查当前文章分类是否允许使用双标题
     * @return bool
     */
    public static function check_term_ids()
    {
        $check_term_ids = array();
        $categorys = get_the_category();
        if (!empty($categorys)) {
            foreach ($categorys as $category) {
                $check_term_ids[] = $category->term_id;
            }
        }
        if (!empty($check_term_ids)) {
            global $double_title_options;
            // 启用分类
            $term_ids = array();
            if (!empty($double_title_options['term_ids'])) {
                $term_ids = $double_title_options['term_ids'];
            }
            if (!empty($term_ids)) {
                $result = array_intersect($check_term_ids, $term_ids);
                return !empty($result);
            }
        }
        return true;
    }

    /**
     * 获取双标题
     * @param int $post_id 文章ID
     * @param string $post_title 文章标题
     * @return string
     */
    public static function get_double_title($post_id, $post_title)
    {
        global $double_title_options;
        // 如果原标题超过了最大长度，则不使用双标题
        if (!empty($double_title_options['len'])) {
            $double_title_len = (int)$double_title_options['len'];
            if ($double_title_len > 0 && mb_strlen($post_title, 'utf-8') > $double_title_len) {
                return $post_title;
            }
        }
        $subtitle = get_post_meta($post_id, 'double_title_subtitle', true);
        $original_title = get_post_meta($post_id, 'double_title_original_title', true);
        // 没有副标题，或者原标题修改了
        if (empty($subtitle) || $subtitle !== $original_title) {
            // 从当前模板匹配
            $auto_result = self::get_auto_title($post_title, $double_title_options['template']);
            if (!empty($auto_result['subtitle'])) {
                $post_title = $auto_result['original_title'];
                $subtitle = $auto_result['subtitle'];
            }
            // 是否从原标题自动匹配副标题
            if (empty($subtitle)) {
                if (!empty($double_title_options['generate_template'])) {
                    $auto_result = self::get_auto_title($post_title, $double_title_options['generate_template']);
                    if (!empty($auto_result['subtitle'])) {
                        $post_title = $auto_result['original_title'];
                        $subtitle = $auto_result['subtitle'];
                    }
                }
            }
            // 从接口获取数据
            if (empty($subtitle)) {
                $subtitle = self::get_subtitle($post_title);
            }
        }
        if (!empty($subtitle) && !empty($post_title)) {
            // 保存标题数据
            update_post_meta($post_id, 'double_title_original_title', $post_title);
            update_post_meta($post_id, 'double_title_subtitle', $subtitle);
            // 替换原标题
            $post_title = preg_replace(array(
                '/[｛{]原标题[}｝]/Uui',
                '/[｛{]副标题[}｝]/Uui',
            ), array(
                $post_title,
                $subtitle
            ), $double_title_options['template']);
        }
        return $post_title;
    }

    /**
     * 根据文章标题获取副标题
     * @param string $post_title 文章标题
     * @return string
     */
    public static function get_subtitle($post_title)
    {
        global $double_title_options;
        $subtitles = Double_Title_Api::get($post_title);;
        if (empty($subtitles)) {
            return '';
        }
        // 按照相似度排序
        $similar = array();
        $data = array();
        foreach ($subtitles as $k => $v) {
            // 如果标题重复，则去掉
            if ($post_title === $v) {
                continue;
            }
            $percent = similar_text($post_title, $v);
            $data[$k] = array(
                'value' => $v,
                'similar' => $percent
            );
            $similar[$k] = $percent;
        }
        if (array_multisort($similar, SORT_ASC, $data)) {
            if (function_exists('array_column')) {
                $subtitles = array_column($data, 'value');
            } else {
                $tmp = array();
                foreach ($data as $v) {
                    $tmp[] = $v['value'];
                }
                $subtitles = $tmp;
            }
        }
        $double_title_similar = 1;
        if (!empty($double_title_options['similar'])) {
            $double_title_similar = $double_title_options['similar'];
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
     * 如果文章没有双标题，则生成双标题
     * @return void
     */
    public static function the_post()
    {
        global $post;
        if ($post->post_status === 'publish' && get_post_type($post->ID) === 'post' && self::check_term_ids()) {
            self::get_double_title($post->ID, $post->post_title);
        }
    }

    /**
     * 引入sweetalert2弹窗组件
     * @return void
     */
    public static function wp_enqueue_scripts()
    {
        if (!empty($_GET['page']) && 'double-title-setting' === $_GET['page']) {
            // 添加静态文件
            // 添加同步文章js文件
            wp_enqueue_script(
                'double-title',
                plugins_url('/js/sweetalert2.min.js', DOUBLE_TITLE_PLUGIN_FILE),
                array(),
                '0.0.4',
                true
            );
            wp_enqueue_script(
                'double-title-sync',
                plugins_url('/js/sync.min.js', DOUBLE_TITLE_PLUGIN_FILE),
                array('jquery'),
                '0.0.4',
                true
            );
            wp_localize_script(
                'double-title-sync',
                'double_title_obj',
                array(
                    'ajax_url' => admin_url('admin-ajax.php'),
                    'nonce' => wp_create_nonce('double_title'),
                )
            );
        }
    }

    /**
     * 批量生成双标题
     * @return void
     */
    public static function wp_ajax_sync_post()
    {
        check_ajax_referer('double_title');
        global $double_title_options;
        // 1 获取总数信息 2 开始预生成标题
        $type = 1;
        if (!empty($_REQUEST['double_type'])) {
            $type = (int)$_REQUEST['double_type'];
        }
        $args = array(
            'post_type' => 'post',
            'post_status' => 'publish',
            'orderby' => 'ID',
            'order' => 'DESC',
            'posts_per_page' => 1
        );
        if (!empty($double_title_options['term_ids'])) {
            $args['category__in'] = $double_title_options['term_ids'];
        }
        $double_end_id = 0;
        $total = 0;
        $has_more = true;
        $post_title = '';
        $post_id = '';
        wp_reset_postdata();
        if (1 == $type) {
            $the_query = new WP_Query($args);
            $total = $the_query->found_posts;
            if ($the_query->have_posts()) {
                while ($the_query->have_posts()) {
                    $the_query->the_post();
                    $post_id = $the_query->post->ID;
                    $double_end_id = $the_query->post->ID + 1;
                }
            } else {
                $has_more = false;
            }
        } else {
            if (!empty($_REQUEST['double_end_id'])) {
                $double_end_id = (int)$_REQUEST['double_end_id'];
            }
            if ($double_end_id <= 0) {
                $has_more = false;
            } else {
                $filter_handler = function ($where) use ($double_end_id) {
                    global $wpdb;
                    return $where . $wpdb->prepare(' AND `' . $wpdb->posts . '`.`ID` < %d', $double_end_id);
                };
                add_filter('posts_where', $filter_handler);
                $the_query = new WP_Query($args);
                $total = $the_query->found_posts;
                if ($the_query->have_posts()) {
                    while ($the_query->have_posts()) {
                        $the_query->the_post();
                        $post_id = $the_query->post->ID;
                        $double_end_id = $post_id;
                        $post_title = self::get_double_title($post_id, $the_query->post->post_title);
                    }
                } else {
                    $has_more = false;
                }
                remove_filter('posts_where', $filter_handler);
            }
        }
        wp_reset_postdata();
        wp_send_json(array(
            'end_id' => $double_end_id,
            'total' => $total,
            'has_more' => $has_more,
            'post_title' => $post_title,
            'post_id' => $post_id,
        ));
    }

    /**
     * 删除文章双标题
     * @return void
     */
    public static function wp_ajax_delete_post()
    {
        check_ajax_referer('double_title');
        delete_metadata('post', 0, 'double_title_original_title', '', true);
        delete_metadata('post', 0, 'double_title_subtitle', '', true);
        wp_send_json(array(
            'status' => 1,
            'msg' => '成功'
        ));
    }
}