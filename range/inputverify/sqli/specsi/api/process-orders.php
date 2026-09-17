<?php
/**
 * Zhazhasu炸炸酥网安靱场团队 - SQL特殊注入场景靶场 - 第一关订单查询接口
 * 版本: v1.0.0
 * 功能: 查询与当前用户关联的服务订单（二次注入漏洞点）
 * 团队: 炸炸酥网安靱场 (Zhazhasu)
 */

$commonBasePath = '../../../../common/';

require_once $commonBasePath . 'includes/Zhazhasu_Database.php';
require_once $commonBasePath . 'includes/session_manager.php';
require_once __DIR__ . '/../includes/functions.php';

Zhazhasu_InitRangeSession('specsi');

// 检查登录状态
$user_id = $_SESSION['specsi_user_id'] ?? 0;
if (!$user_id || ($_SESSION['specsi_level'] ?? 0) !== 1) {
    sendJsonResponse(false, '请先登录');
}

$pdo = Zhazhasu_Database::getConnection('zhazhasu_sqli');

// 第一步：从数据库获取当前用户名（参数化查询，安全）
$stmt = $pdo->prepare("SELECT username FROM zhazhasu_specsi_customers WHERE id = ?");
$stmt->execute([$user_id]);
$username = $stmt->fetchColumn();

if (!$username) {
    sendJsonResponse(false, '用户不存在');
}

// 第二步：使用取出的用户名查询订单
// 从数据库中取出的用户名被直接拼接到SQL中
$sql = "SELECT id, product, amount FROM zhazhasu_specsi_orders WHERE customer = '" . $username . "'";

try {
    $stmt = $pdo->query($sql);
    $results = [];
    if ($stmt) {
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $results[] = $row;
        }
    }
    sendJsonResponse(true, '查询完成', ['results' => $results]);
} catch (PDOException $e) {
    sendJsonResponse(false, '查询失败，请检查输入参数');
}
