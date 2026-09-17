<?php
/**
 * Zhazhasu炸炸酥网安靱场团队 - 优惠滥用靶场 - 第二关登录接口
 * 版本: v1.0.0
 */

header('X-Zhazhasu: Zhazhasu Discount Range v1.0.0');
header('Content-Type: application/json; charset=utf-8');

define('ZHAZHASU_RANGE_ACCESS', true);

$commonBasePath = '../../../../../common/';
require_once $commonBasePath . 'includes/session_manager.php';
require_once $commonBasePath . 'includes/Zhazhasu_Database.php';

Zhazhasu_InitRangeSession('discount');

require_once '../../includes/functions.php';

$data = getRequestData();
$username = isset($data['username']) ? trim($data['username']) : '';
$password = isset($data['password']) ? trim($data['password']) : '';

if (empty($username) || empty($password)) {
    sendJsonResponse(false, '请输入账号和密码');
}

$level = 2;
$pdo = Zhazhasu_Database::getConnection('zhazhasu_logic');

$user = getUser($level, $username, $pdo);

if (!$user || $user['password'] !== $password) {
    sendJsonResponse(false, '账号或密码错误');
}

// 保存登录状态
$_SESSION['discount_user_id_level' . $level] = $user['id'];
$_SESSION['discount_username_level' . $level] = $user['username'];

sendJsonResponse(true, '登录成功', [
    'username' => $user['username'],
    'balance' => floatval($user['balance']),
    'points' => intval($user['points'])
]);
