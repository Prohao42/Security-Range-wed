<?php
/**
 * Zhazhasu炸炸酥网安靱场团队 - CRLF注入靶场登录接口
 * 版本: v1.0.0
 * 创建日期: 2026-03-28
 * 团队: 炸炸酥网安靱场 (Zhazhasu)
 */

header('Content-Type: application/json; charset=utf-8');
header('X-Zhazhasu: Zhazhasu CRLF API v1.0.0');

define('ZHAZHASU_RANGE_ACCESS', true);
$commonBasePath = '../../../../common/';
require_once $commonBasePath . 'includes/session_manager.php';
require_once $commonBasePath . 'includes/Zhazhasu_Database.php';

Zhazhasu_InitRangeSession('crlf');

$input = file_get_contents('php://input');
$data = json_decode($input, true);
$username = isset($data['username']) ? trim($data['username']) : '';
$password = isset($data['password']) ? trim($data['password']) : '';

$response = ['success' => false, 'message' => ''];

try {
    if (empty($username) || empty($password)) {
        throw new Exception('请输入用户名和密码');
    }

    // 获取数据库连接
    $pdo = Zhazhasu_Database::getConnection('zhazhasu_base');

    // 查询用户
    $stmt = $pdo->prepare('SELECT * FROM zhazhasu_crlf_users WHERE username = ?');
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        throw new Exception('用户名或密码错误');
    }

    // 验证密码
    if (!password_verify($password, $user['password'])) {
        throw new Exception('用户名或密码错误');
    }

    // 登录成功，设置会话
    $_SESSION['crlf_user_id'] = $user['id'];
    $_SESSION['crlf_username'] = $user['username'];

    $response['success'] = true;
    $response['message'] = '登录成功';

    // 返回通关状态信息
    if (!empty($user['passcode'])) {
        $response['completed'] = true;
        $response['passcode'] = $user['passcode'];
        $response['completed_at'] = $user['completed_at'];
    } else {
        $response['completed'] = false;
    }

} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

echo json_encode($response, JSON_UNESCAPED_UNICODE);
