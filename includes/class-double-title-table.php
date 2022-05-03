<?php

if (!class_exists('WP_List_Table')) {
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

/**
 * 标题表类
 */
class Double_Title_Table extends WP_List_Table
{
    public function __construct()
    {
        parent::__construct(array(
            'singular' => 'double_title',
            'plural' => 'double_titles',
            'ajax' => false
        ));
    }

    public function get_columns()
    {
        return array(
            'cb' => '<input type="checkbox" />',
            'id' => 'Id',
            'post_id' => '文章ID',
            'original_title' => '原标题',
            'subtitle' => '副标题',
            'create_time' => '创建时间',
            'update_time' => '更新时间'
        );
    }

    public function column_default($item, $column_name)
    {
        if (!empty($item[$column_name])) {
            return $item[$column_name];
        }
        return '';
    }

    public function column_original_title($item)
    {
        $url = get_permalink($item['post_id']);
        if (!empty($url)) {
            return sprintf('<a href="%s" target="_blank">%s</a>', $url, $item['original_title']);
        }
        return $item['original_title'];
    }

    public function column_subtitle($item)
    {
        $page = sanitize_title($_REQUEST['page']);
        $actions = array(
            'edit' => sprintf('<a href="?page=%s&action=%s&id=%d&_wpnonce=%s">编辑</a>', $page, 'edit', $item['id'], wp_create_nonce('bulk-double_titles')),
            'delete' => sprintf('<a href="?page=%s&action=%s&id=%d&_wpnonce=%s">删除</a>', $page, 'delete', $item['id'], wp_create_nonce('bulk-double_titles')),
        );

        return sprintf('%1$s %2$s', $item['subtitle'], $this->row_actions($actions));
    }

    function column_cb($item)
    {
        return sprintf(
            '<input type="checkbox" name="ids[]" value="%d" />', $item['id']
        );
    }

    public function get_bulk_actions()
    {
        return array(
            'sync_post' => '同步文章',
            'sync_title' => '同步标题',
            'delete' => '删除'
        );
    }

    public function prepare_items()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'double_titles';
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = array();
        $this->_column_headers = array($columns, $hidden, $sortable);
        $per_page = 10;
        $current_page = $this->get_pagenum();
        if (1 < $current_page) {
            $offset = $per_page * ($current_page - 1);
        } else {
            $offset = 0;
        }
        $sql = 'SELECT * FROM `' . $table_name . '` ORDER BY id DESC LIMIT %d, %d';
        $items = $wpdb->get_results($wpdb->prepare($sql, $offset, $per_page), ARRAY_A);
        $count = $wpdb->get_var('SELECT COUNT(`id`) FROM `' . $table_name . '`');
        $this->items = $items;
        $this->set_pagination_args(array(
            'total_items' => $count,
            'per_page' => $per_page,
            'total_pages' => ceil($count / $per_page)
        ));
    }

    /**
     * 没有双标题数据
     * @return void
     */
    public function no_items()
    {
        $sync_post_url = self_admin_url('admin.php?page=double-title-list&action=sync_post');
        ?>
        没有双标题文章，<a href="<?php echo esc_url($sync_post_url); ?>">立即导入文章</a>
        <?php
    }
}