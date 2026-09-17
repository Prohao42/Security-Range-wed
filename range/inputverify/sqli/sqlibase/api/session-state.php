<?php
/**
 * Zhazhasu炸炸酥网安靱场团队 - SQL注入基础靶场会话状态接口
 * 版本: v1.0.0
 * 团队: 炸炸酥网安靱场 (Zhazhasu)
 */

define('ZHAZHASU_RANGE_ACCESS', true);
$commonBasePath = '../../../../common/';
require_once __DIR__ . '/../includes/bootstrap.php';

sqlibase_handle_api(function () {
    $state = sqlibase_build_session_state();
    sqlibase_json_success('', $state);
});
