<?php
/**
 * Zhazhasu炸炸酥网安靱场团队 - SQL注入基础靶场搜索资讯接口
 * 版本: v1.0.0
 * 团队: 炸炸酥网安靱场 (Zhazhasu)
 */

define('ZHAZHASU_RANGE_ACCESS', true);
$commonBasePath = '../../../../common/';
require_once __DIR__ . '/../includes/bootstrap.php';

sqlibase_handle_api(function () {
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);

    $category = isset($data['category']) ? $data['category'] : '';

    if ($category === '') {
        sqlibase_json_error('请输入搜索分类');
    }

    $pdo = sqlibase_get_pdo();

    $sql = "SELECT a.id, a.title, a.content, a.publish_date,
                   c.name AS category_name, u.name AS author_name
            FROM zhazhasu_sqlibase_articles a
            LEFT JOIN zhazhasu_sqlibase_categories c ON a.category_id = c.id
            LEFT JOIN zhazhasu_sqlibase_users u ON a.author_id = u.id
            WHERE c.name = \"$category\" AND a.status = 1
            ORDER BY a.publish_date DESC";

    try {
        $stmt = $pdo->query($sql);
        $articles = $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
        if (count($articles) > 0) {
            sqlibase_json_success('', $articles);
        } else {
            sqlibase_json_success('未找到相关资讯', []);
        }
    } catch (PDOException $e) {
        sqlibase_json_success('未找到相关资讯', []);
    }
});
