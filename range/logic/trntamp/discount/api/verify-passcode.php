<?php
/**
 * Zhazhasu炸炸酥网安靱场团队 - 优惠滥用靶场 - 通关密码验证接口
 * 版本: v1.0.0
 */

header('X-Zhazhasu: Zhazhasu Discount Range v1.0.0');
header('Content-Type: application/json; charset=utf-8');

define('ZHAZHASU_RANGE_ACCESS', true);

$commonBasePath = '../../../../common/';
require_once $commonBasePath . 'includes/session_manager.php';
require_once $commonBasePath . 'includes/Zhazhasu_Database.php';

Zhazhasu_InitRangeSession('discount');

require_once '../includes/functions.php';

$data = getRequestData();
$level = isset($data['level']) ? intval($data['level']) : 1;
$passcode = isset($data['passcode']) ? trim($data['passcode']) : '';

if (empty($passcode)) {
    sendJsonResponse(false, '请输入通关密码');
}

$pdo = Zhazhasu_Database::getConnection('zhazhasu_logic');

// 验证通关密码
if (verifyPasscode($passcode, $level, $pdo)) {
    sendJsonResponse(true, '验证通过', ['passed' => true]);
} else {
    sendJsonResponse(false, '通关密码错误');
}
