<?php
/**
 * Zhazhasu炸炸酥网安靱场团队 - SQL不同语句注入靶场 - 第一关修改密码接口
 * 版本: v1.0.0
 * 功能: 用户密码修改（UPDATE语句注入）
 * 团队: 炸炸酥网安靱场 (Zhazhasu)
 */

$commonBasePath = '../../../../common/';

require_once $commonBasePath . 'includes/Zhazhasu_Database.php';
require_once $commonBasePath . 'includes/session_manager.php';
require_once __DIR__ . '/../includes/functions.php';

Zhazhasu_InitRangeSession('cuosi');

$currentUser = $_SESSION['cuosi_username'] ?? null;
if (!$currentUser) {
    sendJsonResponse(false, '请先登录');
}

$oldPassword = $_POST['old_password'] ?? '';
$newPassword = $_POST['new_password'] ?? '';

if ($oldPassword === '' || $newPassword === '') {
    sendJsonResponse(false, '请输入原密码和新密码');
}

$pdo = Zhazhasu_Database::getConnection('zhazhasu_sqli');

// UPDATE语句字符型注入 — SET子句中拼接用户输入
$sql = "UPDATE zhazhasu_cuosi_users SET password = '" . $newPassword
    . "' WHERE username = '" . $currentUser . "' AND password = '" . $oldPassword . "'";

try {
    $stmt = $pdo->query($sql);
    $rowCount = $stmt ? $stmt->rowCount() : 0;
    if ($rowCount > 0) {
        // 密码修改成功，清除登录状态（需重新登录以使角色变更生效）
        unset($_SESSION['cuosi_user_id'], $_SESSION['cuosi_username'], $_SESSION['cuosi_role']);
        sendJsonResponse(true, '密码修改成功，请重新登录');
    } else {
        sendJsonResponse(false, '原密码错误，修改失败');
    }
} catch (PDOException $e) {
    // 将SQL错误信息直接输出到页面
    sendJsonResponse(false, '操作出错：' . $e->getMessage());
}
