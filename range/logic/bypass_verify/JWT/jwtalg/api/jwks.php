<?php
/**
 * Zhazhasu炸炸酥网安靱场团队 - JWT签名算法绕过靶场 - JWKS端点
 * 版本: v1.0.0
 * 创建日期: 2026-03-02
 * 团队: 炸炸酥网安靱场 (Zhazhasu)
 *
 * 说明：返回第三关使用的RSA公钥（JWKS格式）
 */

// 设置响应头
header('X-Zhazhasu: Zhazhasu JWT签名算法绕过 Range v1.0.0');
header('Content-Type: application/json; charset=utf-8');

// 定义访问常量
define('ZHAZHASU_RANGE_ACCESS', true);

// 引入第三关的JWT类
require_once dirname(__DIR__) . '/includes/jwt_level3.php';

try {
    // 获取JWKS格式的公钥
    $jwks = JWT_Level3::getJWKS();

    if (empty($jwks) || empty($jwks['keys'])) {
        echo json_encode([
            'error' => 'Key not found'
        ]);
        exit;
    }

    echo json_encode($jwks);

} catch (Exception $e) {
    error_log('[Zhazhasu] JWKS error: ' . $e->getMessage());
    echo json_encode([
        'error' => 'Internal server error'
    ]);
}
