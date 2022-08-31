<?php

/**
 * 双标题接口
 */
class Double_Title_Api
{
    /**
     * 获取原创标题
     * @return array
     */
    public static function get($title)
    {
        $title = mb_substr(strip_tags($title), 0, 50, 'utf-8');
        $list = array();
        if (!empty($title)) {
            $list = self::getTitlesBySource($title, 1);
        }
        return $list;
    }

    /**
     * 根据来源获取标题
     * @param string $title 原标题
     * @param int $source 来源
     * @return array
     */
    public static function getTitlesBySource($title, $source)
    {
        $title = urlencode($title);
        if ($source == 1) {
            return self::get_baidu($title);
        }
        return array();
    }

    /**
     * 获取指定网址的内容
     * @param string $url
     * @return string
     */
    public static function get_res($url)
    {
        global $double_title_options;
        if (!empty($double_title_options['timeout'])) {
            $timeout = (int)$double_title_options['timeout'];
        } else {
            $timeout = 30;
        }
        $response = wp_remote_get($url, array(
            'timeout' => $timeout,
            'user-agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/104.0.0.0 Safari/537.36',
        ));
        if (is_wp_error($response)) {
            return '';
        }
        return wp_remote_retrieve_body($response);
    }

    /**
     * 获取百度搜索相关词
     * @param string $title
     * @return array
     */
    public static function get_baidu($title)
    {
        $url = 'https://www.baidu.com/sugrec?pre=1&p=3&ie=utf-8&json=1&prod=pc&from=pc_web&wd=' . $title;
        $result = self::get_res($url);
        if (empty($result)) {
            return array();
        }
        $resultArr = json_decode($result, true);
        if (!empty($resultArr['g'])) {
            $data = array();
            if (function_exists('array_column')) {
                $data = array_column($resultArr['g'], 'q');
            } else {
                foreach ($resultArr['g'] as $v) {
                    $data[] = $v['q'];
                }
            }
            return $data;
        }
        return array();
    }
}