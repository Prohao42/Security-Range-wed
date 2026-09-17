<?php
/**
 * Zhazhasu炸炸酥网安靱场团队 - 用户覆盖靶场 - 退出登录接口
 * 版本: v1.0.0
 * 创建日期: 2026-02-25
 * 团队: 炸炸酥网安靱场 (Zhazhasu)
 */

// 设置响应头
header('Content-Type: application/json; charset=utf-8');
header('X-Zhazhasu: Zhazhasu 用户覆盖 Range v1.0.0');

// 定义访问常量
define('ZHAZHASU_RANGE_ACCESS', true);

// 设置公共组件路径
$commonBasePath = '../../../../../common/';

// 引入会话管理
require_once $commonBasePath . 'includes/session_manager.php';

// 初始化会话
Zhazhasu_InitRangeSession('useroverride');

// 清除登录状态
unset($_SESSION['useroverride_logged_in']);
unset($_SESSION['useroverride_user_id']);
unset($_SESSION['useroverride_username']);
unset($_SESSION['useroverride_phone']);
unset($_SESSION['useroverride_is_admin']);
unset($_SESSION['useroverride_admin_verified']);

echo json_encode(['success' => true, 'message' => '已退出登录']);
