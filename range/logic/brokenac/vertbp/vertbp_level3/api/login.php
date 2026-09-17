<?php
/**
 * Zhazhasu炸炸酥网安靱场团队 - 垂直越权基础靶场 - 第三关登录接口
 * 版本: v1.0.0
 * 创建日期: 2026-03-05
 * 团队: 炸炸酥网安靱场 (Zhazhasu)
 */

header('Content-Type: application/json; charset=utf-8');
header('X-Zhazhasu: Zhazhasu API v1.0.0');

define('ZHAZHASU_RANGE_ACCESS', true);
$commonBasePath = '../../../../../common/';
require_once $commonBasePath . 'includes/session_manager.php';
require_once $commonBasePath . 'includes/Zhazhasu_Database.php';
require_once __DIR__ . '/../../includes/user-init.php';

// 初始化靶场会话
Zhazhasu_InitRangeSession('vertbp');

// 获取JSON输入
$input = file_get_contents('php://input');
$data = json_decode($input, true);

$account = isset($data['account']) ? trim($data['account']) : '';
$password = isset($data['password']) ? trim($data['password']) : '';

$response = ['success' => false, 'message' => ''];

try {
    if (empty($account) || empty($password)) {
        throw new Exception('请输入账号和密码');
    }

    // 获取数据库连接
    $pdo = Zhazhasu_Database::getConnection('zhazhasu_logic');

    // 初始化用户数据
    initVertbpLevelUsers(3, $pdo);

    // 查询用户
    $stmt = $pdo->prepare("SELECT * FROM zhazhasu_vertbp_users WHERE level = 3 AND account = ? AND password = ?");
    $stmt->execute([$account, $password]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        throw new Exception('账号或密码错误');
    }

    // 设置会话（安全：角色存储在服务器端Session中）
    $_SESSION['vertbp_level3_logged_in'] = true;
    $_SESSION['vertbp_level3_user'] = $user;

    $response['success'] = true;
    $response['message'] = '登录成功';
    $response['data'] = [
        'account' => $user['account'],
        'role' => $user['role'],
        'redirect' => 'admin.php'
    ];

} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

echo json_encode($response, JSON_UNESCAPED_UNICODE);
