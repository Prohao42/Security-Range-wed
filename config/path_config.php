<?php
/**
 * Zhazhasu 统一路径配置
 * 自动检测并生成项目所需的所有路径
 *
 * @team 炸炸酥网安靱场 (Zhazhasu)
 * @version 1.0.0
 */

// 获取基础URL
// 获取基础URL
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
$host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost';
$scriptName = isset($_SERVER['SCRIPT_NAME']) ? $_SERVER['SCRIPT_NAME'] : __FILE__;
$basePath = rtrim(dirname($scriptName), '/');
$baseUrl = $protocol . '://' . $host . $basePath . '/';

// 只定义基础路径常量
define('ZHAZHASU_BASE_URL', $baseUrl);

/**
 * 获取URL的快捷函数
 * @param string $type URL类型
 * @return string
 */
function zhazhasu_get_url($type = 'base')
{
    switch ($type) {
        case 'api':
            return ZHAZHASU_BASE_URL . 'api/zhazhasu/';
        case 'assets':
            return ZHAZHASU_BASE_URL . 'assets/';
        case 'css':
            return ZHAZHASU_BASE_URL . 'css/';
        case 'js':
            return ZHAZHASU_BASE_URL . 'js/';
        case 'admin':
            return ZHAZHASU_BASE_URL . 'admin/';
        case 'range':
            return ZHAZHASU_BASE_URL . 'range/';
        default:
            return ZHAZHASU_BASE_URL;
    }
}
?>