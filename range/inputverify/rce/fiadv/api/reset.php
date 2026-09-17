<?php
/**
 * Zhazhasu炸炸酥网安靱场团队 - 文件包含进阶靶场重置接口
 * 版本: v1.0.0
 * 创建日期: 2026-04-17
 * 团队: 炸炸酥网安靱场 (Zhazhasu)
 */

define('ZHAZHASU_RANGE_ACCESS', true);

$commonBasePath = '../../../../common/';

require_once $commonBasePath . 'includes/Zhazhasu_Database.php';
require_once __DIR__ . '/../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendJsonResponse(false, '仅支持POST请求');
}

$pdo = Zhazhasu_Database::getConnection('zhazhasu_inputverify');

try {
    $pdo->beginTransaction();

    // 清除成就记录
    $pdo->exec("DELETE FROM zhazhasu_fiadv_achievements");

    // 清除目标字符串
    $pdo->exec("DELETE FROM zhazhasu_fiadv_targets");

    // 生成新的目标字符串
    getOrCreateTargetString($pdo);

    $pdo->commit();

    // 清空 uploads/ 目录
    $uploadDir = dirname(__DIR__) . '/uploads/';
    if (is_dir($uploadDir)) {
        $items = scandir($uploadDir);
        foreach ($items as $item) {
            if ($item === '.' || $item === '..' || $item === '.htaccess') {
                continue;
            }
            $filePath = $uploadDir . $item;
            if (is_file($filePath)) {
                @unlink($filePath);
            }
        }
    }

    sendJsonResponse(true, '靶场已重置');

} catch (Exception $e) {
    $pdo->rollBack();
    sendJsonResponse(false, '重置失败：' . $e->getMessage());
}
