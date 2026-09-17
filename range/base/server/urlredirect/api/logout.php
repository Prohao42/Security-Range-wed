<?php
/**
 * Zhazhasu炸炸酥网安靱场团队 - URL任意跳转靶场退出登录接口
 * 版本: v1.0.0
 * 创建日期: 2026-04-03
 * 团队: 炸炸酥网安靱场 (Zhazhasu)
 */

header('X-Zhazhasu: Zhazhasu URL任意跳转 API v1.0.0');

define('ZHAZHASU_RANGE_ACCESS', true);
$commonBasePath = '../../../../common/';
require_once $commonBasePath . 'includes/session_manager.php';

Zhazhasu_InitRangeSession('urlredirect');

// 清除会话中的登录状态
unset($_SESSION['urlredirect_user_id']);
unset($_SESSION['urlredirect_username']);
unset($_SESSION['urlredirect_logged_in']);

// 跳转到index.php
header('Location: ../index.php');
exit;
