<?php
/**
 * Zhazhasu炸炸酥网安靱场团队 - 条件竞争上传靶场 - 第三关通关验证接口
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

// 引入学习状态更新组件
require_once $commonBasePath . 'includes/Zhazhasu_LearningStatusUpdater.php';

// 获取POST数据
$input = json_decode(file_get_contents('php://input'), true);
$userSecret = isset($input['secret']) ? trim($input['secret']) : '';

// 获取正确的通关密码
$correctSecret = isset($_SESSION['racecondition_level3_secret']) ? $_SESSION['racecondition_level3_secret'] : '';

// 验证密码
if (empty($userSecret)) {
    echo json_encode([
        'success' => false,
        'passed' => false,
        'message' => '请输入通关密码'
    ]);
    exit;
}

if ($userSecret === $correctSecret) {
    // 设置通关状态
    $_SESSION['racecondition_level3_passed'] = true;

    // 更新学习状态
    Zhazhasu_UpdateLearningStatusIfNeeded('racecondition');

    echo json_encode([
        'success' => true,
        'passed' => true,
        'message' => '验证成功！'
    ]);
} else {
    echo json_encode([
        'success' => false,
        'passed' => false,
        'message' => '验证失败，这不是正确的通关密码'
    ]);
}
?>
