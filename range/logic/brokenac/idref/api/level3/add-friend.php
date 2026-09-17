<?php
/**
 * Zhazhasu炸炸酥网安靱场团队 - 水平越权基础靶场 - 第三关添加好友接口
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
Zhazhasu_InitRangeSession('idref');

// 获取JSON输入
$input = file_get_contents('php://input');
$data = json_decode($input, true);

$username = isset($data['username']) ? trim($data['username']) : '';

$response = ['success' => false, 'message' => ''];

try {
    if (empty($username)) {
        throw new Exception('请输入好友账号');
    }

    // 获取数据库连接
    $pdo = Zhazhasu_Database::getConnection('zhazhasu_logic');

    // 初始化第三关用户数据
    initLevelUsers(3, $pdo);

    // 根据账号搜索用户
    $stmt = $pdo->prepare("SELECT account, user_id, name FROM zhazhasu_idref_users WHERE level = 3 AND account = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        throw new Exception('未找到该用户');
    }

    $response['success'] = true;
    $response['message'] = '找到用户：' . $user['name'];
    $response['data'] = [
        'username' => $user['account'],
        'uid' => $user['user_id'],
        'name' => $user['name']
    ];

} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

echo json_encode($response, JSON_UNESCAPED_UNICODE);
