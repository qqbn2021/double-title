<?php

/**
 * 基础类
 */
class Double_Title_Plugin
{
    // 启用插件
    public static function plugin_activation()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'double_titles';
        $charset_collate = $wpdb->get_charset_collate();
        $sql = <<<SQL
CREATE TABLE $table_name (
	`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`post_id` INT(10) UNSIGNED NOT NULL COMMENT '文章ID',
	`original_title` VARCHAR(255) NOT NULL COMMENT '原标题' COLLATE 'utf8mb4_general_ci',
	`subtitle` VARCHAR(255) NOT NULL DEFAULT '' COMMENT '副标题' COLLATE 'utf8mb4_general_ci',
	`create_time` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
	`update_time` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
	PRIMARY KEY (`id`) USING BTREE
) $charset_collate;
SQL;
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
//	dbDelta( $sql );
        // 如果表不存在才会执行创建
        maybe_create_table($table_name, $sql);
        // 创建默认配置
        add_option('double_title_options', array(
            'double_title_menu_position' => 100,
            'double_title_timeout' => 30,
            'double_title_cdkey' => '',
            'double_title_domain' => parse_url(get_home_url(), PHP_URL_HOST),
            'double_title_template' => '{原标题}（{副标题}）',
            'double_title_mode' => 1,
            'double_title_priority' => 10,
            'double_title_similar' => 1,
            'double_title_len' => 0,
            'double_title_wp_title' => 2,
            'double_title_add_name' => 2,
            'double_title_tab_title' => 1,
            'double_title_article_title' => 2,
            'double_title_auto_title' => 1
        ));
    }

    // 删除插件执行的代码
    public static function plugin_uninstall()
    {
        // 删除表
        global $wpdb;
        $table_name = $wpdb->prefix . 'double_titles';
        $wpdb->query('DROP TABLE IF EXISTS `' . $table_name . '`');
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
        $settings_link = '<a href="admin.php?page=double-title-setting">设置</a>';
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
        global $double_title_option;
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
        $options = $double_title_option;
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
        global $double_title_option;
        // 获取菜单位置
        $position = null;
        if (!empty($double_title_option['double_title_menu_position'])) {
            $position = (int)$double_title_option['double_title_menu_position'];
        }

        // 父菜单
        add_menu_page(
            '双标题',
            '双标题',
            'manage_options',
            '#double-title',
            null,
            'dashicons-admin-post',
            $position
        );

        // 设置页面
        add_submenu_page(
            '#double-title',
            '设置',
            '设置',
            'manage_options',
            'double-title-setting',
            array('Double_Title_Plugin', 'show_page')
        );

        // 标题列表
        add_submenu_page(
            '#double-title',
            '双标题列表',
            '双标题列表',
            'manage_options',
            'double-title-list',
            array('Double_Title_List', 'home')
        );

        remove_submenu_page('#double-title', '#double-title');
    }

    // 显示设置页面
    public static function show_page()
    {
        // 检查用户权限
        if (!current_user_can('manage_options')) {
            return;
        }
        // 如果提交了表单，WordPress 会添加 "settings-updated" 参数到 $_GET 里。
        if (!empty($_GET['settings-updated'])) {
            // 添加更新信息
            add_settings_error('double_title_messages', 'double_title_message', '设置已保存。', 'updated');
        }
        // 显示错误/更新信息
        settings_errors('double_title_messages');
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
     * 添加静态文件
     * @return void
     */
    public static function add_static_files()
    {
        if (!empty($_GET['page']) && 'double-title-list' === $_GET['page']) {
            // 添加静态文件
            // 添加同步文章js文件
            wp_enqueue_script(
                'double-title',
                plugins_url('/js/double-title.js', DOUBLE_TITLE_PLUGIN_FILE),
                array('jquery'),
                '0.0.1',
                true
            );
            wp_localize_script(
                'double-title',
                'double_title_obj',
                array(
                    'ajax_url' => admin_url('admin-ajax.php'),
                    'nonce' => wp_create_nonce('double_title'),
                )
            );
        }
    }

    /**
     * 修改标题
     * @param string $title 标题
     * @param int $id 文章ID
     * @return string
     */
    public static function change_title($title, $id)
    {
        global $double_title_option;
        if (!empty($double_title_option['double_title_cdkey']) && !empty($double_title_option['double_title_template']) && get_post_type($id) === 'post') {
            $tmp = self::get_subtitle($id);
            if (!empty($tmp)) {
                $title = $tmp;
            }
        }
        return $title;
    }

    /**
     * wp_title过滤器
     * @param string $title
     * @param string $sep
     * @return string
     */
    public static function wp_title($title, $sep)
    {
        global $double_title_option;
        $id = get_the_ID();
        if (!empty($id) && !empty($double_title_option['double_title_cdkey']) && !empty($double_title_option['double_title_template']) && get_post_type($id) === 'post') {
            $tmp = self::get_subtitle($id);
            if (!empty($tmp)) {
                $title = $tmp;
            }
        }
        $double_title_add_name = 2;
        if (!empty($double_title_option['double_title_add_name'])) {
            $double_title_add_name = (int)$double_title_option['double_title_add_name'];
        }
        return $title . $sep . ($double_title_add_name == 1 ? get_bloginfo('name') : '');
    }

    /**
     * 根据文章ID获取双标题
     * @param int $id
     * @return string
     */
    public static function get_subtitle($id)
    {
        global $wpdb;
        global $double_title_option;
        $title = '';
        $table_name = $wpdb->prefix . 'double_titles';
        $sql = 'SELECT `original_title`,`subtitle` FROM `' . $table_name . '` WHERE `post_id` = %d';
        $query = $wpdb->prepare(
            $sql,
            $id
        );
        $results = $wpdb->get_results($query, 'ARRAY_A');
        if (!empty($results[0])) {
            $double_title_len = 0;
            if (!empty($double_title_option['double_title_len'])) {
                $double_title_len = (int)$double_title_option['double_title_len'];
            }
            $original_title = $results[0]['original_title'];
            // 如果原标题超过了最大长度，则不使用双标题
            if ($double_title_len > 0 && mb_strlen($original_title, 'utf-8') > $double_title_len) {
                return $title;
            }
            $subtitle = $results[0]['subtitle'];
            if (!empty($subtitle) && !empty($original_title)) {
                // 替换原标题
                $title = preg_replace(array(
                    '/[｛{]原标题[}｝]/Uui',
                    '/[｛{]副标题[}｝]/Uui',
                ), array(
                    $original_title,
                    $subtitle
                ), $double_title_option['double_title_template']);
            }
        }
        return $title;
    }

    /**
     * 自动同步文章
     * @param int $post_id
     * @param $post
     * @param $update
     * @return void
     */
    public static function sync_post($post_id, $post, $update)
    {
        if (!wp_is_post_revision($post) && $post->post_status === 'publish' && get_post_type($post_id) === 'post') {
            global $wpdb;
            global $double_title_option;
            $table_name = $wpdb->prefix . 'double_titles';
            $title = $post->post_title;
            $double_title_mode = 1;
            $double_title_auto_title = 2;
            $double_title_template = '';
            if (!empty($double_title_option['double_title_mode'])) {
                $double_title_mode = $double_title_option['double_title_mode'];
            }
            if (!empty($double_title_option['double_title_auto_title'])) {
                $double_title_auto_title = $double_title_option['double_title_auto_title'];
            }
            if (!empty($double_title_option['double_title_template'])) {
                $double_title_template = $double_title_option['double_title_template'];
            }
            $sql = 'SELECT `original_title` FROM `' . $table_name . '` WHERE `post_id` = %d';
            $query = $wpdb->prepare(
                $sql,
                $post_id
            );
            $results = $wpdb->get_results($query, 'ARRAY_A');
            $subtitle = '';
            // 是否从原标题自动匹配副标题
            if (!empty($double_title_template) && $double_title_auto_title == 1) {
                $auto_result = self::get_auto_title($title, $double_title_template);
                if (!empty($auto_result['original_title']) && !empty($auto_result['subtitle'])) {
                    $title = $auto_result['original_title'];
                    $subtitle = $auto_result['subtitle'];
                }
            }
            if (!empty($results[0])) {
                // 更新
                $original_title = $results[0]['original_title'];
                if ($original_title !== $title) {
                    if (empty($subtitle) && $double_title_mode == 2) {
                        $subtitle = Double_Title_List::get_subtitle($title);
                    }
                    $sql = 'UPDATE `' . $table_name . '` SET `original_title` = %s,`subtitle` = %s WHERE `post_id` = %d';
                    $query = $wpdb->prepare(
                        $sql,
                        $title,
                        $subtitle,
                        $post_id
                    );
                    $wpdb->query($query);
                }
            } else {
                // 添加文章标题
                $original_title = $title;
                if (empty($subtitle) && $double_title_mode == 2) {
                    $subtitle = Double_Title_List::get_subtitle($original_title);
                }
                $sql = 'INSERT INTO `' . $table_name . '`(`post_id`,`original_title`,`subtitle`) VALUES (%d,%s,%s)';
                $query = $wpdb->prepare(
                    $sql,
                    $post_id,
                    $original_title,
                    $subtitle
                );
                $wpdb->query($query);
            }
        }
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
        global $double_title_option;
        $id = get_the_ID();
        if (!empty($id) && !empty($double_title_option['double_title_cdkey']) && !empty($double_title_option['double_title_template']) && get_post_type($id) === 'post') {
            $title = self::get_subtitle($id);
            if (!empty($title)) {
                $title_parts_array['title'] = $title;
            }
        }
        return $title_parts_array;
    }
}