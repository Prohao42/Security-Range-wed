<?php
/**
 * Zhazhasu炸炸酥网安靱场团队 - 异常数据处理靶场 - 第三关订单列表接口
 * 版本: v1.0.0
 * 创建日期: 2026-03-18
 * 团队: 炸炸酥网安靱场 (Zhazhasu)
 */

// 设置响应头
header('X-Zhazhasu: Zhazhasu 异常数据 Range v1.0.0');
header('Content-Type: application/json; charset=utf-8');

// 定义访问常量
define('ZHAZHASU_RANGE_ACCESS', true);

// 引入公共组件
$commonBasePath = '../../../../../common/';
require_once $commonBasePath . 'includes/session_manager.php';
require_once $commonBasePath . 'includes/Zhazhasu_Database.php';

// 初始化靶场会话
Zhazhasu_InitRangeSession('anomdata');

// 引入公共函数
require_once '../../includes/functions.php';

$level = 3;

try {
    // 检查是否已登录
    $sessionUserId = isset($_SESSION['anomdata_user_id_level' . $level]) ? $_SESSION['anomdata_user_id_level' . $level] : null;
    if (!$sessionUserId) {
        sendJsonResponse(false, '请先登录');
    }

    // 获取数据库连接
    $pdo = Zhazhasu_Database::getConnection('zhazhasu_logic');

    // 获取订单列表
    $orders = getUserOrders($sessionUserId, $level, $pdo);

    // 处理订单数据，添加has_qrcode标记
    foreach ($orders as &$order) {
        $order['has_qrcode'] = !empty($order['passcode']);
    }

    // 返回结果
    sendJsonResponse(true, '获取成功', [
        'orders' => $orders
    ]);

} catch (Exception $e) {
    error_log('[Zhazhasu] Orders error: ' . $e->getMessage());
    sendJsonResponse(false, '获取订单列表失败');
}
