<?php
/**
 * Zhazhasu炸炸酥网安靱场团队 - SSRF靶场内部元数据接口
 * 版本: v1.0.0
 * 创建日期: 2026-04-06
 * 团队: 炸炸酥网安靱场 (Zhazhasu)
 * 说明: 模拟内网元数据服务接口
 */

header('Content-Type: application/json; charset=utf-8');
header('X-Zhazhasu: Zhazhasu SSRF Range v1.0.0');

define('ZHAZHASU_RANGE_ACCESS', true);

$commonBasePath = '../../../../common/';

require_once $commonBasePath . 'includes/session_manager.php';
require_once $commonBasePath . 'includes/Zhazhasu_Database.php';

Zhazhasu_InitRangeSession('ssrf');

require_once __DIR__ . '/../includes/functions.php';

// 验证访问权限
$token = $_SERVER['HTTP_X_SSRF_TOKEN'] ?? '';
if (empty($token) || !validateSSRFToken($token)) {
    http_response_code(403);
    echo json_encode(['error' => 'Access Denied: 仅限内网访问']);
    exit;
}

// 返回元数据信息
echo json_encode([
    'status' => 'ok',
    'service' => 'Zhazhasu Internal Metadata Service',
    'version' => '1.0.0',
    'hint' => '请读取靶场目录下的config/hit.php文件内容，那里有下一步的指引'
], JSON_UNESCAPED_UNICODE);
