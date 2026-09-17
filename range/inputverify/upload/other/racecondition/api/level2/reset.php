<?php
/**
 * Zhazhasu炸炸酥网安靱场团队 - 条件竞争上传靶场 - 第二关重置接口
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
$tmpDir = $imagesDir . 'tmp/';

// 删除images目录中的所有文件（除了secret.php、.htaccess和tmp目录）
$deletedCount = 0;
if (file_exists($imagesDir)) {
    $files = glob($imagesDir . '*');
    foreach ($files as $file) {
        if (is_file($file)) {
            $basename = basename($file);
            if ($basename !== 'secret.php' && $basename !== '.htaccess') {
                if (unlink($file)) {
                    $deletedCount++;
                }
            }
        }
    }
}

// 清空tmp目录中的所有文件
if (file_exists($tmpDir)) {
    $files = glob($tmpDir . '*');
    foreach ($files as $file) {
        if (is_file($file)) {
            if (unlink($file)) {
                $deletedCount++;
            }
        }
    }
}

// 清除会话中的上传状态
if (isset($_SESSION['racecondition_level2_upload'])) {
    unset($_SESSION['racecondition_level2_upload']);
}

echo json_encode([
    'success' => true,
    'message' => '重置成功，已删除 ' . $deletedCount . ' 个文件'
]);
?>
