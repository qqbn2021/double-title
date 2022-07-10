<?php
/**
 * Plugin Name:双标题
 * Plugin URI:https://www.ggdoc.cn/plugin/2.html
 * Description:支持文章双标题显示，自定义双标题显示模板，支持手动或自动设置文章副标题。
 * Version:0.0.2
 * Requires at least: 5.0
 * Requires PHP:5.3
 * Author:果果开发
 * Author URI:https://www.ggdoc.cn
 * License:GPL v2 or later
 */

// 直接访问报404错误
if (!function_exists('add_action')) {
    http_response_code(404);
    exit;
}

if (defined('DOUBLE_TITLE_PLUGIN_DIR')) {
    // 在我的插件那添加重名插件说明
    add_filter('plugin_action_links_' . plugin_basename(__FILE__), array('Double_Title_Plugin', 'duplicate_name'));
    return;
}

// 插件目录后面有 /
const DOUBLE_TITLE_PLUGIN_FILE = __FILE__;
define('DOUBLE_TITLE_PLUGIN_DIR', plugin_dir_path(DOUBLE_TITLE_PLUGIN_FILE));
// 定义配置
$double_title_option = get_option('double_title_options', array());

/**
 * 自动加载
 * @param string $class
 * @return void
 */
function double_title_autoload($class)
{
    $class_file = DOUBLE_TITLE_PLUGIN_DIR . 'includes/class-' . strtolower(str_replace('_', '-', $class)) . '.php';
    if (file_exists($class_file)) {
        require_once $class_file;
    }
}

spl_autoload_register('double_title_autoload');

// 启用插件
register_activation_hook(__FILE__, array('Double_Title_Plugin', 'plugin_activation'));
// 删除插件
register_uninstall_hook(__FILE__, array('Double_Title_Plugin', 'plugin_uninstall'));
// 添加页面
add_action('admin_init', array('Double_Title_Plugin', 'admin_init'));
// 添加菜单
add_action('admin_menu', array('Double_Title_Plugin', 'admin_menu'));
// 在我的插件那添加设置的链接
add_filter('plugin_action_links_' . plugin_basename(__FILE__), array('Double_Title_Plugin', 'link_double_title'));
// 添加静态文件
add_action('admin_enqueue_scripts', array('Double_Title_Plugin', 'add_static_files'));
// 添加同步文章处理动作
add_action('wp_ajax_sync_post', array('Double_Title_List', 'wp_ajax_sync_post'));
// 添加同步标题处理动作
add_action('wp_ajax_sync_title', array('Double_Title_List', 'wp_ajax_sync_title'));
// 修改副标题
add_action('wp_ajax_update_title', array('Double_Title_List', 'wp_ajax_update_title'));
// 优先级
$priority = 10;
if (!empty($double_title_option['double_title_priority'])) {
    $priority = (int)$double_title_option['double_title_priority'];
}
// 修改文章标题
$double_title_tab_title = 1;
if (!empty($double_title_option['double_title_tab_title'])) {
    $double_title_tab_title = (int)$double_title_option['double_title_tab_title'];
}
$double_title_article_title = 2;
if (!empty($double_title_option['double_title_article_title'])) {
    $double_title_article_title = (int)$double_title_option['double_title_article_title'];
}
$double_title_wp_title = 2;
if (!empty($double_title_option['double_title_wp_title'])) {
    $double_title_wp_title = (int)$double_title_option['double_title_wp_title'];
}
if ($double_title_article_title == 1){
    // 启用页面上的双标题
    add_filter('the_title', array('Double_Title_Plugin', 'change_title'), $priority, 2);
}
if ($double_title_tab_title == 1){
    // 启用标签栏上的双标题
    add_filter('document_title_parts', array('Double_Title_Plugin', 'change_document_title'), $priority);
    if ($double_title_wp_title == 1){
        // 启用wp_title过滤器
        add_filter('wp_title', array('Double_Title_Plugin', 'wp_title'), $priority, 2);
    }
}
// 发布新文章时自动同步
add_action('wp_insert_post', array('Double_Title_Plugin', 'sync_post'), $priority, 3);