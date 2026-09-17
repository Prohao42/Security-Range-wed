<?php
/**
 * Zhazhasu炸炸酥网安靱场团队 - 异常数据处理靶场 - 第三关二维码接口
 * 版本: v1.0.0
 * 创建日期: 2026-03-18
 * 团队: 炸炸酥网安靱场 (Zhazhasu)
 */

// 设置响应头
header('X-Zhazhasu: Zhazhasu 异常数据 Range v1.0.0');

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

// 引入PHP QR Code库
require_once '../../includes/phpqrcode/phpqrcode.php';

$level = 3;

try {
    // 检查是否已登录
    $sessionUserId = isset($_SESSION['anomdata_user_id_level' . $level]) ? $_SESSION['anomdata_user_id_level' . $level] : null;
    if (!$sessionUserId) {
        header('HTTP/1.1 401 Unauthorized');
        exit;
    }

    // 获取订单ID
    $orderId = isset($_GET['orderId']) ? intval($_GET['orderId']) : 0;
    if ($orderId <= 0) {
        header('HTTP/1.1 400 Bad Request');
        exit;
    }

    // 获取数据库连接
    $pdo = Zhazhasu_Database::getConnection('zhazhasu_logic');

    // 获取订单信息
    $stmt = $pdo->prepare("SELECT o.*, p.name as product_name FROM zhazhasu_anomdata_orders o LEFT JOIN zhazhasu_anomdata_products p ON o.product_id = p.id WHERE o.id = ? AND o.user_id = ? AND o.level = ?");
    $stmt->execute([$orderId, $sessionUserId, $level]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$order) {
        header('HTTP/1.1 404 Not Found');
        exit;
    }

    if (empty($order['passcode'])) {
        header('HTTP/1.1 404 Not Found');
        exit;
    }

    // 生成二维码内容
    $qrContent = $order['passcode'];

    // 输出二维码图片
    header('Content-Type: image/png');
    QRcode::png($qrContent, false, QR_ECLEVEL_M, 6, 2);

} catch (Exception $e) {
    error_log('[Zhazhasu] QRCode error: ' . $e->getMessage());
    header('HTTP/1.1 500 Internal Server Error');
    exit;
}
