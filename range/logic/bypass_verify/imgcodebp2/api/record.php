<?php
/**
 * Zhazhasu炸炸酥网安靱场团队 - 成就记录查询接口
 * 版本: v1.0.0
 * 创建日期: 2026-01-20
 * 团队: 炸炸酥网安靱场 (Zhazhasu)
 */

// 设置响应头
header('Content-Type: application/json; charset=utf-8');
header('X-Zhazhasu: Zhazhasu ImgCodeBP2 Record API v1.0.0');

// 设置公共组件路径
$commonBasePath = '../../../../common/';

// 引入数据库组件
require_once $commonBasePath . 'includes/database.php';

// 引入靶场公共配置
require_once '../includes/config.php';

try {
    $db = zhazhasu_db('zhazhasu_logic');

    // 查询所有记录
    $stmt = $db->query("SELECT bypass_type, success_count FROM zhazhasu_imgcodebp2_records ORDER BY bypass_type");
    $records = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 转换为前端所需格式（使用公共配置）
    $formattedRecords = [];
    foreach ($records as $record) {
        $typeName = getImgCodeBP2BypassTypeName($record['bypass_type']);
        $formattedRecords[] = [
            'name' => $typeName,
            'count' => $record['success_count']
        ];
    }

    echo json_encode([
        'success' => true,
        'records' => $formattedRecords,
        'achievedCount' => count($records)
    ], JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    error_log('[Zhazhasu] Record query error: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'records' => [],
        'achievedCount' => 0
    ], JSON_UNESCAPED_UNICODE);
}
?>