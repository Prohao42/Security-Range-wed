<?php
/**
 * Zhazhasu炸炸酥网安靱场团队 - XXE绕过靶场 - 获取已导入数据接口
 * 版本: v1.0.0
 * 创建日期: 2026-04-10
 * 团队: 炸炸酥网安靱场 (Zhazhasu)
 */

header('Content-Type: application/json; charset=utf-8');
header('X-Zhazhasu: Zhazhasu XXEBypass Range v1.0.0');

require_once dirname(__DIR__) . '/includes/functions.php';

$level = isset($_GET['level']) ? intval($_GET['level']) : 1;

if ($level < 1 || $level > 3) {
    sendJsonResponse(false, '无效的关卡编号');
}

$dataPath = getDataFilePath($level);
$products = getImportedData($dataPath);

echo json_encode([
    'success' => true,
    'products' => $products
], JSON_UNESCAPED_UNICODE);
exit;
