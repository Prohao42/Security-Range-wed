<?php
/**
 * Zhazhasu炸炸酥网安靱场团队 - 越权访问综合实战退出接口
 * 版本: v1.0.0
 * 团队: 炸炸酥网安靱场 (Zhazhasu)
 */

define('ZHAZHASU_RANGE_ACCESS', true);
$commonBasePath = '../../../../common/';
require_once __DIR__ . '/../includes/bootstrap.php';

privesc_handle_api(function () {
    privesc_require_method('POST');
    privesc_logout_user();
    privesc_json_success('退出成功');
});
