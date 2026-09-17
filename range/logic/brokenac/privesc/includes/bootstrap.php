<?php
/**
 * Zhazhasu炸炸酥网安靱场团队 - 越权访问综合实战公共引导
 * 版本: v1.0.0
 * 团队: 炸炸酥网安靱场 (Zhazhasu)
 */

if (!defined('ZHAZHASU_RANGE_ACCESS')) {
    define('ZHAZHASU_RANGE_ACCESS', true);
}

$privescCommonBasePath = isset($commonBasePath) ? $commonBasePath : '../../../../common/';

require_once $privescCommonBasePath . 'includes/session_manager.php';
require_once $privescCommonBasePath . 'includes/Zhazhasu_Database.php';
require_once $privescCommonBasePath . 'components/vuln-card/includes/Zhazhasu_VulnManager.php';
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/response.php';
require_once __DIR__ . '/repository.php';
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/vuln-records.php';
require_once __DIR__ . '/user-init.php';

Zhazhasu_InitRangeSession('privesc');
Zhazhasu_ValidateSession();
