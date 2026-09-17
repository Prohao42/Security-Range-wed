<?php
/**
 * Zhazhasu炸炸酥网安靱场团队 - 三方支付漏洞靶场 - 退出登录接口
 * 版本: v1.0.0
 * 创建日期: 2026-03-19
 * 团队: 炸炸酥网安靱场 (Zhazhasu)
 */

// 设置响应头
header('X-Zhazhasu: Zhazhasu 3rdPay Range v1.0.0');
header('Content-Type: application/json; charset=utf-8');

// 定义访问常量
define('ZHAZHASU_RANGE_ACCESS', true);

// 引入公共组件
$commonBasePath = '../../../../common/';
require_once $commonBasePath . 'includes/session_manager.php';

// 初始化靶场会话
Zhazhasu_InitRangeSession('3rdpay');

// 清除电商用户会话
for ($level = 1; $level <= 3; $level++) {
    unset($_SESSION['3rdpay_user_id_level' . $level]);
    unset($_SESSION['3rdpay_username_level' . $level]);
    unset($_SESSION['3rdpay_passcode_level' . $level]);
}

sendJsonResponse(true, '退出成功');
