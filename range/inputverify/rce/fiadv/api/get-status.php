<?php
/**
 * Zhazhasu炸炸酥网安靱场团队 - 文件包含进阶靶场成就状态接口
 * 版本: v1.0.0
 * 创建日期: 2026-04-17
 * 团队: 炸炸酥网安靱场 (Zhazhasu)
 */

define('ZHAZHASU_RANGE_ACCESS', true);

$commonBasePath = '../../../../common/';

require_once $commonBasePath . 'includes/Zhazhasu_Database.php';
require_once __DIR__ . '/../includes/functions.php';

$pdo = Zhazhasu_Database::getConnection('zhazhasu_inputverify');

// 获取目标字符串预览
$targetString = getOrCreateTargetString($pdo);
$targetPreview = substr($targetString, 0, 8) . '********';

// 获取已上传文件列表
$uploadedFiles = getUploadedFilesList();

// 查询成就状态
$achievementData = getAchievementStatus($pdo);

sendJsonResponse(true, '获取成功', [
    'achieved_count' => $achievementData['achieved_count'],
    'target_preview' => $targetPreview,
    'uploaded_files' => $uploadedFiles,
    'records' => $achievementData['records'],
    'progress_hint' => generateProgressHint($achievementData['achieved_count'])
]);
