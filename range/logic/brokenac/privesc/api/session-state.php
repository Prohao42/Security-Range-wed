<?php
/**
 * Zhazhasu炸炸酥网安靱场团队 - 越权访问综合实战获取会话状态接口
 * 版本: v1.0.0
 * 团队: 炸炸酥网安靱场 (Zhazhasu)
 */

define('ZHAZHASU_RANGE_ACCESS', true);
$commonBasePath = '../../../../common/';
require_once __DIR__ . '/../includes/bootstrap.php';

privesc_handle_api(function () {
    privesc_require_method('GET');

    $pdo = privesc_get_pdo();
    privesc_ensure_seed_data($pdo);

    privesc_json_success('', [
        'state' => privesc_build_session_state($pdo),
    ]);
});
