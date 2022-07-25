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
            null,
            null,
            'double_title_page'
        );

        add_settings_field(
            'template',
            // 输入框说明文字
            '文章显示模板',
            array('Double_Title_Plugin', 'double_title_field_callback'),
            'double_title_page',
            'double_title_page_section',
            array(
                'label_for' => 'template',
                'form_type' => 'input',
                'type' => 'text',
                'form_desc' => '原标题使用{原标题}，副标题使用{副标题}。例如：{原标题}（{副标题}）'
            )
        );

        add_settings_field(
            'generate_template',
            // 输入框说明文字
            '文章生成模板',
            array('Double_Title_Plugin', 'double_title_field_callback'),
            'double_title_page',
            'double_title_page_section',
            array(
                'label_for' => 'generate_template',
                'form_type' => 'input',
                'type' => 'text',
                'form_desc' => '原标题使用{原标题}，副标题使用{副标题}。例如：{原标题}（{副标题}）'
            )
        );

        add_settings_field(
            'similar',
            // 输入框说明文字
            '相似度',
            array('Double_Title_Plugin', 'double_title_field_callback'),
            'double_title_page',
            'double_title_page_section',
            array(
                'label_for' => 'similar',
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
                'form_desc' => '原标题和副标题的相似度，按照相似度设置来自动提取副标题'
            )
        );

        add_settings_field(
            'len',
            // 输入框说明文字
            '原标题最大长度',
            array('Double_Title_Plugin', 'double_title_field_callback'),
            'double_title_page',
            'double_title_page_section',
            array(
                'label_for' => 'len',
                'form_type' => 'input',
                'type' => 'number',
                'form_desc' => '当原标题超过最大长度后，此文章不使用双标题。设置为0则不限制最大长度'
            )
        );

        add_settings_field(
            'tab_title',
            // 输入框说明文字
            '启用标签栏上的双标题',
            array('Double_Title_Plugin', 'double_title_field_callback'),
            'double_title_page',
            'double_title_page_section',
            array(
                'label_for' => 'tab_title',
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
            'article_title',
            // 输入框说明文字
            '启用页面上的双标题',
            array('Double_Title_Plugin', 'double_title_field_callback'),
            'double_title_page',
            'double_title_page_section',
            array(
                'label_for' => 'article_title',
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
            'pre_generate',
            // 输入框说明文字
            '预生成双标题',
            array('Double_Title_Plugin', 'double_title_field_callback'),
            'double_title_page',
            'double_title_page_section',
            array(
                'label_for' => 'pre_generate',
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
                'form_desc' => '当文章显示时，生成双标题'
            )
        );

        add_settings_field(
            'wp_title',
            // 输入框说明文字
            '启用wp_title过滤器',
            array('Double_Title_Plugin', 'double_title_field_callback'),
            'double_title_page',
            'double_title_page_section',
            array(
                'label_for' => 'wp_title',
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
            'add_name',
            // 输入框说明文字
            '添加网站名称',
            array('Double_Title_Plugin', 'double_title_field_callback'),
            'double_title_page',
            'double_title_page_section',
            array(
                'label_for' => 'add_name',
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
            'add_sep',
            // 输入框说明文字
            '添加分隔符',
            array('Double_Title_Plugin', 'double_title_field_callback'),
            'double_title_page',
            'double_title_page_section',
            array(
                'label_for' => 'add_sep',
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
            'priority',
            // 输入框说明文字
            '插件优先级',
            array('Double_Title_Plugin', 'double_title_field_callback'),
            'double_title_page',
            'double_title_page_section',
            array(
                'label_for' => 'priority',
                'form_type' => 'input',
                'type' => 'number',
                'form_desc' => '某些插件或主题拥有较高的执行优先级，可以通过设置优先级增加双标题显示成功率'
            )
        );

        add_settings_field(
            'timeout',
            // 输入框说明文字
            '请求超时时间',
            array('Double_Title_Plugin', 'double_title_field_callback'),
            'double_title_page',
            'double_title_page_section',
            array(
                'label_for' => 'timeout',
                'form_type' => 'input',
                'type' => 'number',
                'form_desc' => '请求超时时间，默认为30秒'
            )
        );

        add_settings_field(
            'source',
            // 输入框说明文字
            '数据来源',
            array('Double_Title_Plugin', 'double_title_field_callback'),
            'double_title_page',
            'double_title_page_section',
            array(
                'label_for' => 'source',
                'form_type' => 'checkbox',
                'form_data' => array(
                    array(
                        'title' => '百度',
                        'value' => '1'
                    )
                )
            )
        );

        add_settings_field(
            'delete_btn',
            // 输入框说明文字
            '显示双标题重置按钮',
            array('Double_Title_Plugin', 'double_title_field_callback'),
            'double_title_page',
            'double_title_page_section',
            array(
                'label_for' => 'delete_btn',
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
                'form_desc' => '开启后，将会在后台文章列表页面显示双标题重置按钮'
            )
        );
    }
}