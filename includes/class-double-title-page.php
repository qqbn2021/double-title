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
            array('Double_Title_Page', 'sync_post_text'),
            'double_title_page'
        );

        // 添加指定分类使用
        $terms = get_terms(array(
            'taxonomy' => 'category',
        ));
        if (!empty($terms)) {
            $form_data = array();
            foreach ($terms as $term) {
                $form_data[] = array(
                    'title' => $term->name,
                    'value' => $term->term_id
                );
            }
            add_settings_field(
                'term_ids',
                '分类',
                array('Double_Title_Plugin', 'double_title_field_callback'),
                'double_title_page',
                'double_title_page_section',
                array(
                    'label_for' => 'term_ids',
                    'form_type' => 'checkbox',
                    'form_data' => $form_data,
                    'form_desc' => '指定分类下的文章使用双标题。如果不设置，则全部文章都使用双标题'
                )
            );
        }

        add_settings_field(
            'template',
            '文章标题显示模板',
            array('Double_Title_Plugin', 'double_title_field_callback'),
            'double_title_page',
            'double_title_page_section',
            array(
                'label_for' => 'template',
                'form_type' => 'input',
                'type' => 'text',
                'form_desc' => '页面中文章标题显示的格式。原标题使用{原标题}，副标题使用{副标题}。例如：{原标题}（{副标题}）'
            )
        );

        add_settings_field(
            'generate_template',
            '文章标题生成模板',
            array('Double_Title_Plugin', 'double_title_field_callback'),
            'double_title_page',
            'double_title_page_section',
            array(
                'label_for' => 'generate_template',
                'form_type' => 'input',
                'type' => 'text',
                'form_desc' => '如果之前的文章是双标题格式，需要在此填写原双标题格式。原标题使用{原标题}，副标题使用{副标题}。例如：{原标题}（{副标题}）'
            )
        );

        add_settings_field(
            'similar',
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
            'article_title',
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
            'tab_title',
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
            'wp_title_parts',
            '启用wp_title_parts过滤器',
            array('Double_Title_Plugin', 'double_title_field_callback'),
            'double_title_page',
            'double_title_page_section',
            array(
                'label_for' => 'wp_title_parts',
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
            'priority',
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
    }

    /**
     * 生成文章双标题
     * @return void
     */
    public static function sync_post_text()
    {
        ?>
        <p><a href="javascript:void(0);" id="sync_post" class="button button-small button-secondary" style="margin-right: 20px;">预生成文章双标题</a><a href="javascript:void(0);" id="delete_post" class="button button-small button-secondary">删除文章双标题</a></p>
        <?php
    }
}