<?php
/**
 * Zhazhasu炸炸酥网安靱场团队 - 代码注入靶场 - 获取备份文件列表接口
 * 版本: v1.0.0
 * 创建日期: 2026-04-22
 * 团队: 炸炸酥网安靱场 (Zhazhasu)
 */

header('Content-Type: application/json; charset=utf-8');
header('X-Zhazhasu: Zhazhasu CodeInj Range v1.0.0');

require_once dirname(__DIR__) . '/includes/functions.php';

$backupDir = dirname(__DIR__) . '/backups/';
$backups = [];

if (is_dir($backupDir)) {
    $files = scandir($backupDir);
    foreach ($files as $file) {
        if ($file === '.' || $file === '..' || $file === '.htaccess') {
            continue;
        }
        $filepath = $backupDir . $file;
        if (is_file($filepath)) {
            $backups[] = [
                'filename' => $file,
                'filesize' => filesize($filepath)
            ];
        }
    }
}

sendJsonResponse(true, '', ['backups' => $backups]);
