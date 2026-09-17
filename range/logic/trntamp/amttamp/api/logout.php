<?php
/**
 * Zhazhasu炸炸酥网安靱场团队 - 金额篡改靶场 - 退出登录接口
 * 版本: v1.0.0
 * 创建日期: 2026-03-17
 * 团队: 炸炸酥网安靱场 (Zhazhasu)
 */

// 设置响应头
header('X-Zhazhasu: Zhazhasu 金额篡改 Range v1.0.0');
header('Content-Type: application/json; charset=utf-8');

// 定义访问常量
define('ZHAZHASU_RANGE_ACCESS', true);

// 引入公共组件
$commonBasePath = '../../common/';
require_once $commonBasePath . 'includes/session_manager.php';

// 初始化靶场会话
Zhazhasu_InitRangeSession('amttamp');

// 清除所有关卡的登录状态
for ($level = 1; $level <= 3; $level++) {
    unset($_SESSION['amttamp_user_id_level' . $level]);
    unset($_SESSION['amttamp_username_level' . $level]);
}

// 返回成功响应
header('Content-Type: application/json; charset=utf-8');
echo json_encode([
    'success' => true,
    'message' => '退出成功'
]);
