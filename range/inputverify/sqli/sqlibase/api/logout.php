<?php
/**
 * Zhazhasu炸炸酥网安靱场团队 - SQL注入基础靶场退出登录接口
 * 版本: v1.0.0
 * 团队: 炸炸酥网安靱场 (Zhazhasu)
 */

define('ZHAZHASU_RANGE_ACCESS', true);
$commonBasePath = '../../../../common/';
require_once __DIR__ . '/../includes/bootstrap.php';

sqlibase_handle_api(function () {
    sqlibase_require_method('POST');
    sqlibase_logout();
    sqlibase_json_success('已退出登录');
});
