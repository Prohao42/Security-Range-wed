<?php
/**
 * Zhazhasu炸炸酥网安靱场团队 - 密码重置凭证可猜测靶场 - 第三关发送重置链接API
 * 版本: v1.0.0
 * 创建日期: 2026-01-22
 * 团队: 炸炸酥网安靱场 (Zhazhasu)
 */

header('Content-Type: application/json; charset=utf-8');
header('X-Zhazhasu: Zhazhasu API v1.0.0');

define('ZHAZHASU_RANGE_ACCESS', true);
$commonBasePath = '../../../../../../common/';
require_once $commonBasePath . 'includes/Zhazhasu_Database.php';
require_once $commonBasePath . 'components/sms-simulator/includes/Zhazhasu_SmsSender.php';

$input = file_get_contents('php://input');
$data = json_decode($input, true);
$username = isset($data['username']) ? trim($data['username']) : '';

$response = ['success' => false, 'message' => ''];

try {
    $pdo = Zhazhasu_Database::getConnection('zhazhasu_logic');
    $stmt = $pdo->prepare("SELECT * FROM zhazhasu_resetlink_users WHERE level = 3 AND username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        throw new Exception('账号不存在');
    }

    // 使用账号+手机号+当前时间戳的SHA256值作为凭证
    $timestamp = time();
    $token = hash('sha256', $user['username'] . $user['phone'] . $timestamp);
    // 动态获取网站基础URL
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    // 计算靶场根目录的相对URL路径
    $rangeDir = dirname(dirname(dirname($_SERVER['SCRIPT_NAME']))); // 从 /api/level3/ 返回到靶场根目录
    $baseUrl = $protocol . '://' . $host . $rangeDir;
    $resetLink = $baseUrl . '/reset3.php?token=' . $token;
    $message = '您的密码重置链接为：' . $resetLink . ' （链接有效期1小时）';

    $result = Zhazhasu_SmsSender::send($user['phone'], $message, 'resetlink_level3');

    if ($result['success']) {
        $response['success'] = true;
        $response['message'] = '重置链接已发送到手机号：' . $user['phone'];
        // 返回时间戳供前端显示
        $response['timestamp'] = $timestamp;
        $response['expires_in'] = 3600; // 1小时有效期
    } else {
        throw new Exception($result['message'] ?? '发送失败');
    }
} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

echo json_encode($response, JSON_UNESCAPED_UNICODE);
