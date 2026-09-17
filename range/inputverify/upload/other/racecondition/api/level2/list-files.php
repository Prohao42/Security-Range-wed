<?php
/**
 * Zhazhasu炸炸酥网安靱场团队 - 条件竞争上传靶场 - 第二关获取文件列表接口
 * 版本: v1.0.0
 * 团队: 炸炸酥网安靱场 (Zhazhasu)
 */

// 设置响应头
header('X-Zhazhasu: Zhazhasu 条件竞争上传 Range v1.0.0');
header('Content-Type: application/json; charset=utf-8');

// 公共组件路径
$commonBasePath = '../../../../../../common/';

define('ZHAZHASU_RANGE_ACCESS', true);

// 引入会话管理
require_once $commonBasePath . 'includes/session_manager.php';
Zhazhasu_InitRangeSession('racecondition');
Zhazhasu_ValidateSession();

// 目录路径
$imagesDir = dirname(__DIR__, 2) . '/images/';

// 获取文件列表
$uploadedFiles = [];

// 获取images目录下的文件
if (file_exists($imagesDir)) {
    $files = scandir($imagesDir);
    foreach ($files as $file) {
        if ($file !== '.' && $file !== '..' && $file !== 'secret.php' && $file !== '.htaccess' && $file !== 'tmp') {
            $uploadedFiles[] = [
                'name' => $file,
                'path' => 'images/' . $file,
                'size' => filesize($imagesDir . $file)
            ];
        }
    }
}

echo json_encode([
    'success' => true,
    'files' => $uploadedFiles
]);
?>
