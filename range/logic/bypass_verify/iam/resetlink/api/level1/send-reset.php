<?php
/**
 * Zhazhasu炸炸酥网安靱场团队 - 密码重置凭证可猜测靶场 - 第一关发送重置链接API
 * 版本: v1.0.0
 * 创建日期: 2026-01-22
 * 团队: 炸炸酥网安靱场 (Zhazhasu)
 *

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
    $stmt = $pdo->prepare("SELECT * FROM zhazhasu_resetlink_users WHERE level = 1 AND username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        throw new Exception('账号不存在');
    }


    // 动态获取网站基础URL
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    // 计算靶场根目录的相对URL路径
    $rangeDir = dirname(dirname(dirname($_SERVER['SCRIPT_NAME']))); // 从 /api/level1/ 返回到靶场根目录
    $baseUrl = $protocol . '://' . $host . $rangeDir;
    $resetLink = $baseUrl . '/reset1.php?uid=' . $user['user_id'];
    $message = '您的密码重置链接为：' . $resetLink;

    $result = Zhazhasu_SmsSender::send($user['phone'], $message, 'resetlink_level1');

    if ($result['success']) {
        $response['success'] = true;
        $response['message'] = '重置链接已发送到手机号：' . $user['phone'];
    } else {
        throw new Exception($result['message'] ?? '发送失败');
    }
} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

echo json_encode($response, JSON_UNESCAPED_UNICODE);
