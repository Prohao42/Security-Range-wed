<?php
/**
 * Zhazhasu炸炸酥网安靱场团队 - 用户覆盖靶场 - 图片验证码接口
 * 版本: v1.0.0
 * 创建日期: 2026-02-25
 * 团队: 炸炸酥网安靱场 (Zhazhasu)
 */

// 定义访问常量
define('ZHAZHASU_RANGE_ACCESS', true);

// 设置公共组件路径
$commonBasePath = '../../../../../common/';

// 引入会话管理
require_once $commonBasePath . 'includes/session_manager.php';
Zhazhasu_InitRangeSession('useroverride');

// 引入验证码类
require_once $commonBasePath . 'classes/Zhazhasu_Captcha.php';

// 生成验证码
$captcha = new Zhazhasu_Captcha(120, 40, 4, 20, 'useroverride_captcha', $commonBasePath);
$captcha->generate();
