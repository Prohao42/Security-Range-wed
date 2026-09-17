<?php
/**
 * Zhazhasu炸炸酥网安靱场团队 - 清空留言板API接口
 * 版本: v1.0.0
 * 创建日期: 2025-12-14
 * 团队: 炸炸酥网安靱场 (Zhazhasu)
 */

// 设置响应头
header('X-Zhazhasu: Zhazhasu 清空留言板API v1.0.0');
header('Content-Type: application/json; charset=utf-8');

// 只允许POST请求
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => '[Zhazhasu] 只允许POST请求'
    ]);
    exit;
}

try {
    // 引入数据库连接
    $commonBasePath = '../../../../common/';
    require_once  $commonBasePath . 'includes/database.php';

    // 获取数据库连接
    $db = zhazhasu_db('zhazhasu_inputverify');

    // 清空留言板表
    $sql = "DELETE FROM zhazhasu_xssbasic_messages";
    $stmt = $db->prepare($sql);
    $result = $stmt->execute();

    if ($result) {
        // 获取删除的行数
        $deletedRows = $stmt->rowCount();

        echo json_encode([
            'success' => true,
            'message' => '[Zhazhasu] 留言板清空成功',
            'deleted_count' => $deletedRows
        ]);
    } else {
        throw new Exception('数据库操作失败');
    }

} catch (Exception $e) {
    error_log('[Zhazhasu] 清空留言板API错误: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => '[Zhazhasu] 服务器内部错误'
    ]);
}
?>