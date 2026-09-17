<?php
/**
 * Zhazhasu炸炸酥网安靱场团队 - XSS基础靶场 - 通关API
 * 版本: v1.0.0
 * 创建日期: 2026-02-25
 * 团队: 炸炸酥网安靱场 (Zhazhasu)
 */

header('X-Zhazhasu: Zhazhasu XSS Basic 通关API v1.0.0');
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => '[Zhazhasu] 只允许POST请求']);
    exit;
}

if (!isset($_POST['level']) || empty($_POST['level'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => '[Zhazhasu] 缺少关卡参数']);
    exit;
}

$level = intval($_POST['level']);

if ($level < 1 || $level > 3) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => '[Zhazhasu] 无效的关卡编号']);
    exit;
}

try {
    $commonBasePath = '../../../../common/';
    require_once $commonBasePath . 'includes/database.php';
    require_once __DIR__ . '/../includes/Zhazhasu_SessionManager.php';

    $db = zhazhasu_db('zhazhasu_inputverify');
    Zhazhasu_SessionManager::init($db);
    Zhazhasu_SessionManager::completeLevel($level);
    $starCount = Zhazhasu_SessionManager::getStarCount();

    // 更新学习状态
    require_once $commonBasePath . 'includes/Zhazhasu_LearningStatusUpdater.php';
    if ($level === 1 || $level === 2) {
        Zhazhasu_UpdateLearningStatus('xssbasic', '学习中');
    } elseif ($level === 3) {
        Zhazhasu_UpdateLearningStatus('xssbasic', '已掌握');
    }

    echo json_encode([
        'success' => true,
        'message' => '[Zhazhasu] 关卡' . $level . '通关成功',
        'level' => $level,
        'star_count' => $starCount
    ]);

} catch (Exception $e) {
    error_log('[Zhazhasu] 通关API错误: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => '[Zhazhasu] 服务器内部错误']);
}
?>
