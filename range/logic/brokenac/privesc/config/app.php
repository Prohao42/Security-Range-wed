<?php
/**
 * Zhazhasu炸炸酥网安靱场团队 - 越权访问综合实战靶场基础配置
 * 版本: v1.0.0
 * 团队: 炸炸酥网安靱场 (Zhazhasu)
 */

return [
    'version' => 'v1.0.0',
    'range_code' => 'privesc',
    'database' => 'zhazhasu_logic',
    'tables' => [
        'users' => 'zhazhasu_privesc_users',
        'addresses' => 'zhazhasu_privesc_address',
        'vuln_records' => 'zhazhasu_privesc_vuln_records',
        'star_status' => 'zhazhasu_privesc_star_status',
    ],
    'cookie' => [
        'type_name' => 'type',
        'path' => '',
        'lifetime' => 3600,
        'samesite' => 'Lax',
    ],
    'upload' => [
        'allowed_mime' => ['image/png', 'image/jpeg'],
        'extensions' => ['png', 'jpg', 'jpeg'],
        'relative_dir' => 'uploads/avatars/',
        'absolute_dir' => dirname(__DIR__) . '/uploads/avatars/',
    ],
    'roles' => [
        0 => '普通用户',
        2 => '管理员',
    ],
    'default_admin' => [
        'username' => 'admin',
        'name' => '关莉媛',
        'phone' => '13800138000',
        'address' => '福州市鼓楼区炸炸酥网安靱场大厦 36 层',
    ],
    'default_user' => [
        'address' => '请填写您的联系地址',
    ],
    'ui' => [
        'title' => '越权访问漏洞挖掘',
        'star_count' => 3,
        'score_thresholds' => [400, 800, 1200],
        'star_titles' => ['越权初探者', '权限猎手', '提权专家'],
        'max_score' => 1200,
        'vuln_types' => ['权限提升', '水平越权', '垂直越权'],
    ],
];
