<?php

/**
 * 基础设置
 */
class Double_Title_Page
{
    // 初始化页面
    public static function init_page()
    {
        // 注册一个新页面
        register_setting('double_title_page', 'double_title_options');

        add_settings_section(
            'double_title_page_section',
            '授权码说明',
            array('Double_Title_Page', 'double_title_page_callback'),
            'double_title_page'
        );

        // 在新的设置页面添加表单输入框
        add_settings_field(
            'double_title_menu_position',
            // 输入框说明文字
            '菜单显示位置',
            array('Double_Title_Plugin', 'double_title_field_callback'),
            'double_title_page',
            'double_title_page_section',
            array(
                'label_for' => 'double_title_menu_position',
                'form_type' => 'select',
                'form_data' => array(
                    array(
                        'title' => '文章',
                        'value' => '5'
                    ),
                    array(
                        'title' => '媒体',
                        'value' => '10'
                    ),
                    array(
                        'title' => '页面',
                        'value' => '20'
                    ),
                    array(
                        'title' => '评论',
                        'value' => '25'
                    ),
                    array(
                        'title' => '插件',
                        'value' => '65'
                    ),
                    array(
                        'title' => '用户',
                        'value' => '70'
                    ),
                    array(
                        'title' => '工具',
                        'value' => '75'
                    ),
                    array(
                        'title' => '设置',
                        'value' => '80'
                    ),
                    array(
                        'title' => '默认',
                        'value' => '100'
                    )
                )
            )
        );

        add_settings_field(
            'double_title_timeout',
            // 输入框说明文字
            '请求超时时间',
            array('Double_Title_Plugin', 'double_title_field_callback'),
            'double_title_page',
            'double_title_page_section',
            array(
                'label_for' => 'double_title_timeout',
                'form_type' => 'input',
                'type' => 'number',
                'form_desc' => '请求超时时间，默认为30秒'
            )
        );

        add_settings_field(
            'double_title_cdkey',
            // 输入框说明文字
            '授权码',
            array('Double_Title_Plugin', 'double_title_field_callback'),
            'double_title_page',
            'double_title_page_section',
            array(
                'label_for' => 'double_title_cdkey',
                'form_type' => 'input',
                'type' => 'text',
                'form_desc' => '授权码为32位字符串'
            )
        );

        add_settings_field(
            'double_title_template',
            // 输入框说明文字
            '双标题模板',
            array('Double_Title_Plugin', 'double_title_field_callback'),
            'double_title_page',
            'double_title_page_section',
            array(
                'label_for' => 'double_title_template',
                'form_type' => 'input',
                'type' => 'text',
                'form_desc' => '原标题使用{原标题}，副标题使用{副标题}。例如：{原标题}（{副标题}）'
            )
        );

        add_settings_field(
            'double_title_mode',
            // 输入框说明文字
            '生成模式',
            array('Double_Title_Plugin', 'double_title_field_callback'),
            'double_title_page',
            'double_title_page_section',
            array(
                'label_for' => 'double_title_mode',
                'form_type' => 'select',
                'form_data' => array(
                    array(
                        'title' => '手动生成',
                        'value' => '1'
                    ),
                    array(
                        'title' => '自动生成',
                        'value' => '2'
                    )
                ),
                'form_desc' => '手动生成需要自己手动从接口数据中选择一个副标题，而自动生成则会自动从接口数据中选择一个副标题'
            )
        );

        add_settings_field(
            'double_title_auto_title',
            // 输入框说明文字
            '从原标题匹配副标题',
            array('Double_Title_Plugin', 'double_title_field_callback'),
            'double_title_page',
            'double_title_page_section',
            array(
                'label_for' => 'double_title_auto_title',
                'form_type' => 'select',
                'form_data' => array(
                    array(
                        'title' => '是',
                        'value' => '1'
                    ),
                    array(
                        'title' => '否',
                        'value' => '2'
                    )
                ),
                'form_desc' => '如果您的文章原标题本来就是双标题，可以通过此功能自动匹配到副标题'
            )
        );

        add_settings_field(
            'double_title_similar',
            // 输入框说明文字
            '相似度',
            array('Double_Title_Plugin', 'double_title_field_callback'),
            'double_title_page',
            'double_title_page_section',
            array(
                'label_for' => 'double_title_similar',
                'form_type' => 'select',
                'form_data' => array(
                    array(
                        'title' => '高',
                        'value' => '1'
                    ),
                    array(
                        'title' => '中',
                        'value' => '2'
                    ),
                    array(
                        'title' => '低',
                        'value' => '3'
                    )
                ),
                'form_desc' => '原标题和副标题的相似度，自动生成模式将按照相似度设置来自动提取副标题'
            )
        );

        add_settings_field(
            'double_title_len',
            // 输入框说明文字
            '原标题最大长度',
            array('Double_Title_Plugin', 'double_title_field_callback'),
            'double_title_page',
            'double_title_page_section',
            array(
                'label_for' => 'double_title_len',
                'form_type' => 'input',
                'type' => 'number',
                'form_desc' => '当原标题超过最大长度后，此文章不使用双标题。设置为0则不限制最大长度'
            )
        );

        add_settings_field(
            'double_title_tab_title',
            // 输入框说明文字
            '启用标签栏上的双标题',
            array('Double_Title_Plugin', 'double_title_field_callback'),
            'double_title_page',
            'double_title_page_section',
            array(
                'label_for' => 'double_title_tab_title',
                'form_type' => 'select',
                'form_data' => array(
                    array(
                        'title' => '是',
                        'value' => '1'
                    ),
                    array(
                        'title' => '否',
                        'value' => '2'
                    )
                ),
                'form_desc' => '开启后，将会开启标签栏双标题，搜索引擎将会收录此标题'
            )
        );

        add_settings_field(
            'double_title_article_title',
            // 输入框说明文字
            '启用页面上的双标题',
            array('Double_Title_Plugin', 'double_title_field_callback'),
            'double_title_page',
            'double_title_page_section',
            array(
                'label_for' => 'double_title_article_title',
                'form_type' => 'select',
                'form_data' => array(
                    array(
                        'title' => '是',
                        'value' => '1'
                    ),
                    array(
                        'title' => '否',
                        'value' => '2'
                    )
                ),
                'form_desc' => '开启后，文章列表页面、详情页面上的标题都会显示为双标题。建议不开启'
            )
        );

        add_settings_field(
            'double_title_wp_title',
            // 输入框说明文字
            '启用wp_title过滤器',
            array('Double_Title_Plugin', 'double_title_field_callback'),
            'double_title_page',
            'double_title_page_section',
            array(
                'label_for' => 'double_title_wp_title',
                'form_type' => 'select',
                'form_data' => array(
                    array(
                        'title' => '是',
                        'value' => '1'
                    ),
                    array(
                        'title' => '否',
                        'value' => '2'
                    )
                ),
                'form_desc' => '如果无法修改标签栏上的标题，可以设置为是'
            )
        );

        add_settings_field(
            'double_title_add_name',
            // 输入框说明文字
            '添加网站名称',
            array('Double_Title_Plugin', 'double_title_field_callback'),
            'double_title_page',
            'double_title_page_section',
            array(
                'label_for' => 'double_title_add_name',
                'form_type' => 'select',
                'form_data' => array(
                    array(
                        'title' => '是',
                        'value' => '1'
                    ),
                    array(
                        'title' => '否',
                        'value' => '2'
                    )
                ),
                'form_desc' => '某些插件或主题可能会自动添加网站名称，设置为是后，插件会在wp_title过滤器中添加网站名称'
            )
        );

        add_settings_field(
            'double_title_add_sep',
            // 输入框说明文字
            '添加分隔符',
            array('Double_Title_Plugin', 'double_title_field_callback'),
            'double_title_page',
            'double_title_page_section',
            array(
                'label_for' => 'double_title_add_sep',
                'form_type' => 'select',
                'form_data' => array(
                    array(
                        'title' => '是',
                        'value' => '1'
                    ),
                    array(
                        'title' => '否',
                        'value' => '2'
                    )
                ),
                'form_desc' => '某些插件或主题可能会自动在标题和网站名称之间添加分隔符，设置为是后，插件会在wp_title过滤器中添加分隔符'
            )
        );

        add_settings_field(
            'double_title_priority',
            // 输入框说明文字
            '插件优先级',
            array('Double_Title_Plugin', 'double_title_field_callback'),
            'double_title_page',
            'double_title_page_section',
            array(
                'label_for' => 'double_title_priority',
                'form_type' => 'input',
                'type' => 'number',
                'form_desc' => '某些插件或主题拥有较高的执行优先级，可以通过设置优先级增加双标题成功率'
            )
        );
    }

    /**
     * 授权码文字说明
     * @return void
     */
    public static function double_title_page_callback()
    {
        ?>
        如果您还没有购买授权码，请点击此链接去购买：<a href="https://dev.ggdoc.cn/plugin/2.html" target="_blank">https://dev.ggdoc.cn/plugin/2.html</a>。
        <?php
    }
}