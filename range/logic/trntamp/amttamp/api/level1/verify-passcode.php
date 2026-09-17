<?php
/**
 * Zhazhasu炸炸酥网安靱场团队 - 金额篡改靶场 - 第一关通关密码验证接口
 * 版本: v1.0.0
 * 创建日期: 2026-03-14
 * 团队: 炸炸酥网安靱场 (Zhazhasu)
 */

// 设置响应头
header('X-Zhazhasu: Zhazhasu 金额篡改 Range v1.0.0');
header('Content-Type: application/json; charset=utf-8');

// 定义访问常量
define('ZHAZHASU_RANGE_ACCESS', true);

// 引入公共组件
$commonBasePath = '../../../../../common/';
require_once $commonBasePath . 'includes/session_manager.php';
require_once $commonBasePath . 'includes/Zhazhasu_Database.php';

// 初始化靶场会话
Zhazhasu_InitRangeSession('amttamp');

// 引入公共函数
require_once '../../includes/functions.php';

// 获取POST数据
$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (!$data) {
    $data = $_POST;
}

// 获取参数
$passcode = isset($data['passcode']) ? trim($data['passcode']) : '';
$level = 1;

// 验证参数
if (empty($passcode)) {
    sendJsonResponse(false, '请输入通关密码');
}

try {
    // 验证通关密码
    if (verifyPasscode($passcode, $level)) {
        sendJsonResponse(true, '验证通过', ['passed' => true]);
    } else {
        sendJsonResponse(false, '通关密码错误', ['passed' => false]);
    }

} catch (Exception $e) {
    error_log('[Zhazhasu] Verify passcode error: ' . $e->getMessage());
    sendJsonResponse(false, '验证失败，请稍后重试');
}
