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
            'wp_title' => 2,
            'add_name' => 2,
            'add_sep' => 2,
            'tab_title' => 1,
            'pre_generate' => 2,
            'delete_btn' => 1,
            'article_title' => 2,
            'source' => array(1)
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
        if (!empty($_GET['post_id'])) {
            $post_id = intval($_GET['post_id']);
            delete_post_meta($post_id, 'double_title_original_title');
            delete_post_meta($post_id, 'double_title_subtitle');
            add_settings_error('double_titles', 'double_title_message', '重置双标题成功。', 'updated');
            // 显示错误/更新信息
            settings_errors('double_titles');
            $location = $_SERVER['HTTP_REFERER'];
            echo '<script>location.href="' . esc_url($location) . '";</script>';
            exit();
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
        global $double_title_options;
        if (!empty($double_title_options['template']) && get_post_type($id) === 'post') {
            $tmp = self::get_double_title($id, $title);
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
        global $double_title_options;
        $id = get_the_ID();
        if (!empty($id) && !empty($double_title_options['template']) && get_post_type($id) === 'post') {
            $tmp = self::get_double_title($id, $title);
            if (!empty($tmp)) {
                $title = $tmp;
            }
        }
        $double_title_add_name = 2;
        if (!empty($double_title_options['add_name'])) {
            $double_title_add_name = (int)$double_title_options['add_name'];
        }
        $double_title_add_sep = 2;
        if (!empty($double_title_options['add_sep'])) {
            $double_title_add_sep = (int)$double_title_options['add_sep'];
        }
        return $title . ($double_title_add_sep === 1 ? $sep : '') . ($double_title_add_name === 1 ? get_bloginfo('name') : '');
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
        global $double_title_options;
        $id = get_the_ID();
        if (!empty($id) && !empty($double_title_options['template']) && get_post_type($id) === 'post') {
            $title = self::get_double_title($id, $title_parts_array['title']);
            if (!empty($title)) {
                $title_parts_array['title'] = $title;
            }
        }
        return $title_parts_array;
    }

    /**
     * 在插件页面添加同名插件处理问题
     *
     * @param $links
     *
     * @return mixed
     */
    public static function duplicate_name($links)
    {
        $settings_link = '<a href="https://www.ggdoc.cn/plugin/2.html" target="_blank">请删除其它版本《双标题》插件</a>';
        array_unshift($links, $settings_link);

        return $links;
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
            // 是否从原标题自动匹配副标题
            if (!empty($double_title_options['generate_template'])) {
                $auto_result = self::get_auto_title($post_title, $double_title_options['generate_template']);
                if (!empty($auto_result['subtitle'])) {
                    $post_title = $auto_result['original_title'];
                    $subtitle = $auto_result['subtitle'];
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
     * 后台文章列表显示双标题删除按钮
     * @param array $columns
     * @return array
     */
    public static function manage_post_posts_columns($columns)
    {
        return array_merge($columns, array('double_title' => '双标题'));
    }

    /**
     * 后台文章列表显示双标题删除按钮
     * @param string $column_key
     * @param int $post_id
     * @return void
     */
    public static function manage_post_posts_custom_column($column_key, $post_id)
    {
        if ($column_key == 'double_title') {
            $subtitle = get_post_meta($post_id, 'double_title_subtitle', true);
            if (!empty($subtitle)) {
                echo '<a href="options-general.php?page=double-title-setting&post_id=' . esc_attr($post_id) . '" title="重置双标题">重置</a>';
            } else {
                echo '<span style="color:red;">未生成</span>';
            }
        }
    }

    /**
     * 如果文章没有双标题，则生成双标题
     * @return void
     */
    public static function the_post()
    {
        global $post;
        if ($post->post_status === 'publish') {
            self::get_double_title($post->ID, $post->post_title);
        }
    }
}