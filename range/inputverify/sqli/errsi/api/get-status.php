<?php
/**
 * Zhazhasu炸炸酥网安靱场团队 - 报错注入靶场 - 成就状态接口
 * 版本: v1.0.0
 * 创建日期: 2026-04-18
 * 功能: 获取当前成就状态和服务列表
 * 团队: 炸炸酥网安靱场 (Zhazhasu)
 */

define('ZHAZHASU_RANGE_ACCESS', true);

$commonBasePath = '../../../../common/';

require_once $commonBasePath . 'includes/Zhazhasu_Database.php';
require_once __DIR__ . '/../includes/functions.php';

$pdo = Zhazhasu_Database::getConnection('zhazhasu_sqli');

// 查询成就状态
$achievementData = getAchievementStatus($pdo);

sendJsonResponse(true, '获取成功', [
    'achieved_count' => $achievementData['achieved_count'],
    'records'        => $achievementData['records'],
    'progress_hint'  => generateProgressHint($achievementData['achieved_count'])
]);
