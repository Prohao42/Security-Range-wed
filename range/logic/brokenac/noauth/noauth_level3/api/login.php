<?php
/**
 * Zhazhasu炸炸酥网安靱场团队 - 未授权访问靶场 - 第三关登录接口
 */

header('Content-Type: application/json; charset=utf-8');
header('X-Zhazhasu: Zhazhasu API v1.0.0');

define('ZHAZHASU_RANGE_ACCESS', true);
$commonBasePath = '../../../../../common/';
require_once $commonBasePath . 'includes/session_manager.php';
require_once $commonBasePath . 'includes/Zhazhasu_Database.php';
require_once __DIR__ . '/../../includes/config-init.php';

Zhazhasu_InitRangeSession('noauth');

$input = file_get_contents('php://input');
$data = json_decode($input, true);
$account = isset($data['account']) ? trim($data['account']) : '';
$password = isset($data['password']) ? trim($data['password']) : '';

$response = ['success' => false, 'message' => ''];

try {
    if (!$account || !$password) {
        throw new Exception('请输入账号和密码');
    }

    $pdo = Zhazhasu_Database::getConnection('zhazhasu_logic');
    $config = initNoauthLevelConfig(3, $pdo);

    if ($account !== 'admin') {
        throw new Exception('账号或密码错误');
    }

    if ($password !== $config['admin_password']) {
        throw new Exception('账号或密码错误');
    }

    $_SESSION['noauth_level3_logged_in'] = true;
    $_SESSION['noauth_level3_user'] = [
        'account' => 'admin',
        'role' => 'admin'
    ];

    $response['success'] = true;
    $response['message'] = '登录成功';

} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

echo json_encode($response, JSON_UNESCAPED_UNICODE);
