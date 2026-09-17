<?php
/**
 * Zhazhasu炸炸酥网安靱场团队 - SSRF漏洞靶场重置接口
 * 版本: v1.0.0
 * 创建日期: 2026-04-06
 * 团队: 炸炸酥网安靱场 (Zhazhasu)
 * 说明: 重置靶场数据，清除进度和秘密
 */

header('Content-Type: application/json; charset=utf-8');
header('X-Zhazhasu: Zhazhasu SSRF Range v1.0.0');

define('ZHAZHASU_RANGE_ACCESS', true);

$commonBasePath = '../../../../common/';

require_once $commonBasePath . 'includes/session_manager.php';
require_once $commonBasePath . 'includes/Zhazhasu_Database.php';

Zhazhasu_InitRangeSession('ssrf');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => '请求方法不允许']);
    exit;
}

require_once __DIR__ . '/../includes/functions.php';

try {
    $pdo = Zhazhasu_Database::getConnection('zhazhasu_base');
    $sessionId = session_id();

    $result = resetRangeData($pdo, $sessionId);

    if ($result) {
        echo json_encode(['success' => true, 'message' => '靶场已重置']);
    } else {
        echo json_encode(['success' => false, 'message' => '重置失败，请稍后重试']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => '服务器内部错误']);
}
