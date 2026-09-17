<?php
/**
 * Zhazhasu炸炸酥网安靱场团队 - 异常数据处理靶场 - 第三关登录接口
 * 版本: v1.0.0
 * 创建日期: 2026-03-18
 * 团队: 炸炸酥网安靱场 (Zhazhasu)
 */

// 设置响应头
header('X-Zhazhasu: Zhazhasu 异常数据 Range v1.0.0');
header('Content-Type: application/json; charset=utf-8');

// 定义访问常量
define('ZHAZHASU_RANGE_ACCESS', true);

// 引入公共组件
$commonBasePath = '../../../../../common/';
require_once $commonBasePath . 'includes/session_manager.php';
require_once $commonBasePath . 'includes/Zhazhasu_Database.php';

// 初始化靶场会话
Zhazhasu_InitRangeSession('anomdata');

// 引入公共函数
require_once '../../includes/functions.php';

// 获取POST数据
$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (!$data) {
    $data = $_POST;
}

// 获取参数
$username = isset($data['username']) ? trim($data['username']) : '';
$password = isset($data['password']) ? trim($data['password']) : '';
$level = 3;

// 验证参数
if (empty($username) || empty($password)) {
    sendJsonResponse(false, '请输入账号和密码');
}

try {
    // 获取数据库连接
    $pdo = Zhazhasu_Database::getConnection('zhazhasu_logic');

    // 查询用户
    $user = getUser($level, $username, $pdo);

    if (!$user || $user['password'] !== $password) {
        sendJsonResponse(false, '账号或密码错误');
    }

    // 保存用户ID到会话
    $_SESSION['anomdata_user_id_level' . $level] = $user['id'];
    $_SESSION['anomdata_username_level' . $level] = $user['username'];

    // 获取商品列表
    $products = getProducts($level, $pdo);

    // 检查是否已购买商品
    $passcode = null;
    if (hasPurchasedProduct($user['id'], $level, $pdo)) {
        $passcode = getPasscode($level);
        if (!$passcode) {
            $passcode = generatePasscode($level);
        }
    }

    // 返回用户信息
    sendJsonResponse(true, '登录成功', [
        'username' => $user['username'],
        'balance' => floatval($user['balance']),
        'products' => $products,
        'passcode' => $passcode
    ]);

} catch (Exception $e) {
    error_log('[Zhazhasu] Login error: ' . $e->getMessage());
    sendJsonResponse(false, '登录失败，请稍后重试');
}
