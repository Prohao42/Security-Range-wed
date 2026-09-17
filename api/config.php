<?php
/**
 * Zhazhasu 炸炸酥网安靱场团队 - 前台API配置文件
 * 使用增强版路径管理系统
 *
 * @package Zhazhasu_API
 * @version 2.0.0
 * @author 炸炸酥网安靱场 (Zhazhasu)
 * @copyright 炸炸酥网安靱场 (Zhazhasu) 2026
 */

// 引入Zhazhasu系统引导文件
require_once __DIR__ . '/../bootstrap.php';

// 创建数据库连接（使用Zhazhasu统一连接方式）
function getConnection() {
    return Zhazhasu_getConnection();
}

// 统一返回格式（使用Zhazhasu统一返回格式）
function returnResponse($success, $message, $data = null) {
    return Zhazhasu_returnResponse($success, $message, $data);
}

// 错误处理（使用Zhazhasu统一错误处理）
function handleError($message) {
    return Zhazhasu_handleError($message);
}
?>