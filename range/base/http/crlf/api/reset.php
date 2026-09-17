<?php
/**
 * Zhazhasu炸炸酥网安靱场团队 - CRLF注入靶场重置接口
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

$response = ['success' => false, 'message' => ''];

try {
    // 获取数据库连接
    $pdo = Zhazhasu_Database::getConnection('zhazhasu_base');

    // 重置所有用户的通关状态
    $stmt = $pdo->prepare('UPDATE zhazhasu_crlf_users SET passcode = NULL, completed_at = NULL');
    $stmt->execute();

    // 清除会话
    unset($_SESSION['crlf_user_id']);
    unset($_SESSION['crlf_username']);
    unset($_SESSION['zhazhasu_secret']);

    $response['success'] = true;
    $response['message'] = '重置成功';

} catch (Exception $e) {
    $response['message'] = '重置失败：' . $e->getMessage();
}

echo json_encode($response, JSON_UNESCAPED_UNICODE);
