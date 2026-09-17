<?php
/**
 * Zhazhasu炸炸酥网安靱场团队 - 时间盲注靶场 - 重置接口
 * 版本: v2.0.0
 * 创建日期: 2026-04-20
 * 更新日期: 2026-04-26 - 双维度成就表清除
 * 功能: 重置靶场成就数据（延迟技术 + 字符串函数）
 * 团队: 炸炸酥网安靱场 (Zhazhasu)
 */

define('ZHAZHASU_RANGE_ACCESS', true);

$commonBasePath = '../../../../common/';

require_once $commonBasePath . 'includes/Zhazhasu_Database.php';
require_once __DIR__ . '/../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendJsonResponse(false, '仅支持POST请求');
}

$pdo = Zhazhasu_Database::getConnection('zhazhasu_sqli');

try {
    $pdo->beginTransaction();

    // 清除延迟技术成就记录和字符串函数成就记录
    $pdo->exec("DELETE FROM zhazhasu_timesi_achievements");
    $pdo->exec("DELETE FROM zhazhasu_timesi_string_functions");

    $pdo->commit();
    sendJsonResponse(true, '靶场已重置');
} catch (PDOException $e) {
    $pdo->rollBack();
    sendJsonResponse(false, '重置失败');
}
