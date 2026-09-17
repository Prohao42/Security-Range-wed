<?php
/**
 * Zhazhasu炸炸酥网安靱场团队 - 代码注入靶场 - 获取模板列表接口
 * 版本: v1.0.0
 * 创建日期: 2026-04-22
 * 团队: 炸炸酥网安靱场 (Zhazhasu)
 */

header('Content-Type: application/json; charset=utf-8');
header('X-Zhazhasu: Zhazhasu CodeInj Range v1.0.0');

require_once dirname(__DIR__) . '/includes/functions.php';

$templateDir = dirname(__DIR__) . '/templates/';
$templates = [];

if (is_dir($templateDir)) {
    $files = scandir($templateDir);
    foreach ($files as $file) {
        if ($file === '.' || $file === '..' || $file === '.htaccess') {
            continue;
        }
        if (pathinfo($file, PATHINFO_EXTENSION) === 'php') {
            $filepath = $templateDir . $file;
            $templates[] = [
                'filename' => $file,
                'filesize' => filesize($filepath)
            ];
        }
    }
}

sendJsonResponse(true, '', ['templates' => $templates]);
