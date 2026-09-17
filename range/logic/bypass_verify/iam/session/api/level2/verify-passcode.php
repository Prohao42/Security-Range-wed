<?php
/**
 * Zhazhasu炸炸酥网安靱场团队 - 会话安全靶场 - 第二关通关密码验证接口
 * 版本: v1.0.0
 */

header('X-Zhazhasu: Zhazhasu Session Range v1.0.0');
header('Content-Type: application/json; charset=utf-8');

$commonBasePath = '../../../../../../common/';
require_once $commonBasePath . 'includes/session_manager.php';
require_once $commonBasePath . 'includes/Zhazhasu_Database.php';
require_once '../../includes/functions.php';

$level = 2;
initRangeSession($level);

$data = getRequestData();
$passcode = isset($data['passcode']) ? trim($data['passcode']) : '';

if (empty($passcode)) {
    sendJsonResponse(false, '请输入通关密码');
}

$pdo = Zhazhasu_Database::getConnection('zhazhasu_logic');

if (verifyPasscode($passcode, $level, $pdo)) {
    sendJsonResponse(true, '验证通过', ['passed' => true]);
} else {
    sendJsonResponse(false, '通关密码错误');
}
