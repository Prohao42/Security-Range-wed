<?php
/**
 * Zhazhasu炸炸酥网安靱场团队 - 科技蓝UI组件库PHP渲染器
 * Tech Blue UI Component Library PHP Renderer
 * 版本: v1.0.0
 * 创建日期: 2025-11-11
 * 团队: 炸炸酥网安靱场 (Zhazhasu)
 * 描述: 科技蓝风格UI组件的PHP渲染类
 */



class Zhazhasu_TechBlue {
    
    /**
     * 版本信息
     */
    const VERSION = '1.0.0';
    
    /**
     * 组件配置缓存
     */
    private static $config = null;
    
    /**
     * 资源版本号
     */
    private static $resourceVersion = '1.0.0';
    
    /**
     * 初始化组件库
     */
    public static function init($options = []) {
        $defaults = [
            'theme' => 'default',
            'animations' => true,
            'autoInit' => true,
            'version' => self::$resourceVersion
        ];
        
        self::$config = array_merge($defaults, $options);
        
        return self::renderAssets($options);
    }
    
    /**
     * 渲染组件资源
     */
    public static function renderAssets($options = [], $commonBasePath = null) {
        $config = array_merge(self::$config ?: [], $options);

        $html = '';

        // CSS资源
        if (isset($config['css']) ? $config['css'] : true) {
            $html .= self::renderCSS($config, $commonBasePath);
        }

        // JavaScript资源
        if (isset($config['js']) ? $config['js'] : true) {
            $html .= self::renderJavaScript($config, $commonBasePath);
        }

        // 初始化脚本
        if (isset($config['autoInit']) ? $config['autoInit'] : true) {
            $html .= self::renderInitScript($config);
        }

        return $html;
    }
    
    /**
     * 渲染CSS资源
     */
    private static function renderCSS($config, $commonBasePath = null) {
        $version = isset($config['version']) ? $config['version'] : self::$resourceVersion;
        $theme = isset($config['theme']) ? $config['theme'] : 'default';

        // 构建基础路径
        $basePath = self::getBasePath($commonBasePath);

        $css = <<<CSS
<!-- Zhazhasu Tech Blue UI Components CSS -->
<link rel="stylesheet" href="{$basePath}components/tech-blue/css/zhazhasu-tech-blue-variables.css?v={$version}">
<link rel="stylesheet" href="{$basePath}components/tech-blue/css/zhazhasu-tech-blue-animations.css?v={$version}">
<link rel="stylesheet" href="{$basePath}components/tech-blue/css/zhazhasu-tech-blue.css?v={$version}">
CSS;

        return $css;
    }
    
    /**
     * 渲染JavaScript资源
     */
    private static function renderJavaScript($config, $commonBasePath = null) {
        $version = isset($config['version']) ? $config['version'] : self::$resourceVersion;

        // 构建基础路径
        $basePath = self::getBasePath($commonBasePath);

        $js = <<<JS
<!-- Zhazhasu Tech Blue UI Components JavaScript -->
<script src="{$basePath}components/tech-blue/js/zhazhasu-tech-blue-config.js?v={$version}"></script>
<script src="{$basePath}components/tech-blue/js/zhazhasu-tech-blue.js?v={$version}"></script>
JS;

        return $js;
    }
    
    /**
     * 渲染初始化脚本
     */
    private static function renderInitScript($config) {
        $theme = isset($config['theme']) ? $config['theme'] : 'default';
        $animations = $config['animations'] ? 'true' : 'false';
        
        $script = <<<SCRIPT
<script>
// 初始化Zhazhasu Tech Blue组件库
document.addEventListener('DOMContentLoaded', function() {
    Zhazhasu.TechBlue.init({
        animations: {
            enabled: {$animations},
            pageLoad: true,
            hoverEffects: true,
            transitions: true
        },
        performance: {
            gpuAcceleration: true,
            reduceMotion: false
        },
        accessibility: {
            keyboardNavigation: true,
            focusManagement: true,
            ariaLabels: true
        }
    });
    
    // 应用主题
    ZhazhasuTechBlueConfig.applyTheme('{$theme}');
});
</script>
SCRIPT;
        
        return $script;
    }
    
    /**
     * 渲染卡片组件
     */
    public static function renderCard($data = []) {
        $defaults = [
            'id' => '',
            'title' => '卡片标题',
            'subtitle' => '',
            'content' => '卡片内容',
            'footer' => '',
            'variant' => 'default', // default, elevated, flat, glass
            'size' => 'medium', // small, medium, large
            'hover' => true,
            'animation' => 'fadeIn', // fadeIn, slideIn, scaleIn, none
            'class' => '',
            'attributes' => []
        ];
        
        $config = array_merge($defaults, $data);
        
        $id = $config['id'] ? ' id="' . htmlspecialchars($config['id']) . '"' : '';
        $variantClass = $config['variant'] !== 'default' ? ' zhazhasu-tech-card-' . $config['variant'] : '';
        $sizeClass = $config['size'] !== 'medium' ? ' zhazhasu-tech-card-' . $config['size'] : '';
        $hoverClass = $config['hover'] ? ' zhazhasu-tech-hover-lift' : '';
        $animationClass = $config['animation'] !== 'none' ? ' zhazhasu-tech-animate-' . $config['animation'] : '';
        $customClass = $config['class'] ? ' ' . htmlspecialchars($config['class']) : '';
        
        $attributes = '';
        if (!empty($config['attributes'])) {
            foreach ($config['attributes'] as $attr => $value) {
                $attributes .= ' ' . htmlspecialchars($attr) . '="' . htmlspecialchars($value) . '"';
            }
        }
        
        $subtitleHtml = $config['subtitle'] ? '<p class="zhazhasu-tech-card-subtitle">' . htmlspecialchars($config['subtitle']) . '</p>' : '';
        $footerHtml = $config['footer'] ? '<div class="zhazhasu-tech-card-footer">' . $config['footer'] . '</div>' : '';
        
        ob_start();
        ?>
        <div class="zhazhasu-tech-card<?php echo $variantClass . $sizeClass . $hoverClass . $animationClass . $customClass; ?>"<?php echo $id . $attributes; ?>>
            <?php if ($config['title'] || $config['subtitle']): ?>
            <div class="zhazhasu-tech-card-header">
                <?php if ($config['title']): ?>
                <h3 class="zhazhasu-tech-card-title"><?php echo htmlspecialchars($config['title']); ?></h3>
                <?php endif; ?>
                <?php echo $subtitleHtml; ?>
            </div>
            <?php endif; ?>
            
            <div class="zhazhasu-tech-card-body">
                <?php echo $config['content']; ?>
            </div>
            
            <?php echo $footerHtml; ?>
        </div>
        <?php
        return ob_get_clean();
    }
    
    /**
     * 渲染按钮组件
     */
    public static function renderButton($data = []) {
        $defaults = [
            'text' => '按钮',
            'type' => 'button', // button, submit, reset, link
            'variant' => 'primary', // primary, secondary, success, warning, danger, info
            'size' => 'medium', // small, medium, large
            'outline' => false,
            'rounded' => true,
            'disabled' => false,
            'loading' => false,
            'icon' => '',
            'iconPosition' => 'left', // left, right
            'href' => '',
            'onClick' => '',
            'class' => '',
            'attributes' => []
        ];
        
        $config = array_merge($defaults, $data);
        
        $tag = $config['type'] === 'link' ? 'a' : 'button';
        $sizeClass = $config['size'] !== 'medium' ? ' zhazhasu-tech-btn-' . $config['size'] : '';
        $outlineClass = $config['outline'] ? ' zhazhasu-tech-btn-outline' : '';
        $roundedClass = $config['rounded'] ? ' zhazhasu-tech-btn-rounded' : '';
        $loadingClass = $config['loading'] ? ' zhazhasu-tech-loading' : '';
        $customClass = $config['class'] ? ' ' . htmlspecialchars($config['class']) : '';
        
        $disabledAttr = $config['disabled'] ? ' disabled' : '';
        $hrefAttr = $config['href'] ? ' href="' . htmlspecialchars($config['href']) . '"' : '';
        $onClickAttr = $config['onClick'] ? ' onclick="' . htmlspecialchars($config['onClick']) . '"' : '';
        
        $attributes = '';
        if (!empty($config['attributes'])) {
            foreach ($config['attributes'] as $attr => $value) {
                $attributes .= ' ' . htmlspecialchars($attr) . '="' . htmlspecialchars($value) . '"';
            }
        }
        
        $iconHtml = '';
        if ($config['icon']) {
            $iconClass = $config['icon'][0] === '<' ? $config['icon'] : '<i class="fa ' . htmlspecialchars($config['icon']) . '"></i>';
            $iconHtml = '<span class="zhazhasu-tech-btn-icon">' . $iconClass . '</span>';
        }
        
        $iconLeft = $config['icon'] && $config['iconPosition'] === 'left' ? $iconHtml : '';
        $iconRight = $config['icon'] && $config['iconPosition'] === 'right' ? $iconHtml : '';
        
        $loadingHtml = $config['loading'] ? '<span class="zhazhasu-tech-btn-loading"></span>' : '';
        
        ob_start();
        ?>
        <<?php echo $tag; ?> class="zhazhasu-tech-btn zhazhasu-tech-btn-<?php echo htmlspecialchars($config['variant']); ?><?php echo $sizeClass . $outlineClass . $roundedClass . $loadingClass . $customClass; ?>"<?php echo $disabledAttr . $hrefAttr . $onClickAttr . $attributes; ?>>
            <?php echo $iconLeft; ?>
            <span class="zhazhasu-tech-btn-text"><?php echo htmlspecialchars($config['text']); ?></span>
            <?php echo $iconRight; ?>
            <?php echo $loadingHtml; ?>
        </<?php echo $tag; ?>>
        <?php
        return ob_get_clean();
    }
    
    /**
     * 渲染提示框组件
     */
    public static function renderAlert($data = []) {
        $defaults = [
            'message' => '提示信息',
            'type' => 'info', // success, warning, error, info
            'title' => '',
            'dismissible' => false,
            'icon' => true,
            'autoClose' => 0, // 毫秒，0表示不自动关闭
            'position' => 'top-right', // top-left, top-center, top-right
            'class' => '',
            'attributes' => []
        ];

        $config = array_merge($defaults, $data);

        // 映射type到目标站点的类型
        $typeMapping = [
            'success' => 'success',
            'warning' => 'warning',
            'danger' => 'error',
            'info' => 'info'
        ];

        $alertType = isset($typeMapping[$config['type']]) ? $typeMapping[$config['type']] : $config['type'];

        $id = 'zhazhasu-tech-alert-' . uniqid();
        $customClass = $config['class'] ? ' ' . htmlspecialchars($config['class']) : '';
        $autoCloseAttr = $config['autoClose'] > 0 ? ' data-auto-close="' . $config['autoClose'] . '"' : '';

        $attributes = '';
        if (!empty($config['attributes'])) {
            foreach ($config['attributes'] as $attr => $value) {
                $attributes .= ' ' . htmlspecialchars($attr) . '="' . htmlspecialchars($value) . '"';
            }
        }

        $iconMap = [
            'success' => 'fa-check-circle',
            'warning' => 'fa-exclamation-triangle',
            'error' => 'fa-times-circle',
            'info' => 'fa-info-circle'
        ];

        $iconHtml = '';
        if ($config['icon']) {
            $iconClass = isset($iconMap[$alertType]) ? $iconMap[$alertType] : $iconMap['info'];
            $iconHtml = '<i class="fa ' . $iconClass . '"></i>';
        }

        // 构建消息内容，包含标题
        $messageContent = $config['title'] ? '<strong>' . htmlspecialchars($config['title']) . '</strong>' : '';
        if ($config['title'] && $config['message']) {
            $messageContent .= ' ' . $config['message'];
        } elseif ($config['message']) {
            $messageContent = $config['message'];
        }

        ob_start();
        ?>
        <div class="alert alert-<?php echo htmlspecialchars($alertType); ?><?php echo $customClass; ?>" id="<?php echo $id; ?>"<?php echo $autoCloseAttr . $attributes; ?>>
            <?php echo $iconHtml; ?>
            <?php echo $messageContent; ?>
            <?php if ($config['dismissible']): ?>
                <button class="zhazhasu-tech-alert-close" style="margin-left: auto; background: none; border: none; color: inherit; cursor: pointer;">
                    <i class="fa fa-times"></i>
                </button>
            <?php endif; ?>
        </div>
        <?php
        return ob_get_clean();
    }
    
    /**
     * 渲染模态框组件
     */
    public static function renderModal($data = []) {
        $defaults = [
            'id' => '',
            'title' => '模态框标题',
            'content' => '模态框内容',
            'footer' => '',
            'size' => 'medium', // small, medium, large, fullscreen
            'closeOnEscape' => true,
            'closeOnOverlay' => true,
            'showCloseButton' => true,
            'centered' => true,
            'backdrop' => true,
            'animation' => 'fadeIn', // fadeIn, slideIn, scaleIn
            'class' => '',
            'attributes' => []
        ];
        
        $config = array_merge($defaults, $data);
        
        $id = $config['id'] ?: 'zhazhasu-tech-modal-' . uniqid();
        $sizeClass = $config['size'] !== 'medium' ? ' zhazhasu-tech-modal-' . $config['size'] : '';
        $centeredClass = $config['centered'] ? ' zhazhasu-tech-modal-centered' : '';
        $animationClass = $config['animation'] !== 'fadeIn' ? ' zhazhasu-tech-modal-' . $config['animation'] : '';
        $customClass = $config['class'] ? ' ' . htmlspecialchars($config['class']) : '';
        
        $attributes = '';
        if (!empty($config['attributes'])) {
            foreach ($config['attributes'] as $attr => $value) {
                $attributes .= ' ' . htmlspecialchars($attr) . '="' . htmlspecialchars($value) . '"';
            }
        }
        
        $closeButtonHtml = $config['showCloseButton'] ? '<button class="zhazhasu-tech-modal-close" data-bs-dismiss="modal"><i class="fa fa-times"></i></button>' : '';
        $footerHtml = $config['footer'] ? '<div class="zhazhasu-tech-modal-footer">' . $config['footer'] . '</div>' : '';
        
        ob_start();
        ?>
        <div class="zhazhasu-tech-modal<?php echo $sizeClass . $centeredClass . $animationClass . $customClass; ?>" id="<?php echo $id; ?>"<?php echo $attributes; ?>>
            <?php if ($config['backdrop']): ?>
            <div class="zhazhasu-tech-modal-overlay" <?php echo $config['closeOnOverlay'] ? 'data-dismiss="modal"' : ''; ?>></div>
            <?php endif; ?>
            
            <div class="zhazhasu-tech-modal-content">
                <div class="zhazhasu-tech-modal-header">
                    <h3 class="zhazhasu-tech-modal-title"><?php echo htmlspecialchars($config['title']); ?></h3>
                    <?php echo $closeButtonHtml; ?>
                </div>
                
                <div class="zhazhasu-tech-modal-body">
                    <?php echo $config['content']; ?>
                </div>
                
                <?php echo $footerHtml; ?>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
    
    /**
     * 渲染表格组件
     */
    public static function renderTable($data = []) {
        $defaults = [
            'headers' => [],
            'rows' => [],
            'striped' => true,
            'bordered' => false,
            'hover' => true,
            'responsive' => true,
            'class' => '',
            'attributes' => []
        ];
        
        $config = array_merge($defaults, $data);
        
        $stripedClass = $config['striped'] ? ' zhazhasu-tech-table-striped' : '';
        $borderedClass = $config['bordered'] ? ' zhazhasu-tech-table-bordered' : '';
        $hoverClass = $config['hover'] ? ' zhazhasu-tech-table-hover' : '';
        $customClass = $config['class'] ? ' ' . htmlspecialchars($config['class']) : '';
        
        $attributes = '';
        if (!empty($config['attributes'])) {
            foreach ($config['attributes'] as $attr => $value) {
                $attributes .= ' ' . htmlspecialchars($attr) . '="' . htmlspecialchars($value) . '"';
            }
        }
        
        ob_start();
        ?>
        <?php if ($config['responsive']): ?>
        <div class="zhazhasu-tech-table-responsive">
        <?php endif; ?>
        
        <table class="zhazhasu-tech-table<?php echo $stripedClass . $borderedClass . $hoverClass . $customClass; ?>"<?php echo $attributes; ?>>
            <?php if (!empty($config['headers'])): ?>
            <thead>
                <tr>
                    <?php foreach ($config['headers'] as $header): ?>
                    <th><?php echo htmlspecialchars($header); ?></th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <?php endif; ?>
            
            <tbody>
                <?php foreach ($config['rows'] as $row): ?>
                <tr>
                    <?php foreach ($row as $cell): ?>
                    <td><?php echo $cell; ?></td>
                    <?php endforeach; ?>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <?php if ($config['responsive']): ?>
        </div>
        <?php endif; ?>
        <?php
        return ob_get_clean();
    }
    
    /**
     * 渲染表单组件
     */
    public static function renderForm($data = []) {
        $defaults = [
            'fields' => [],
            'method' => 'post',
            'action' => '',
            'class' => '',
            'attributes' => []
        ];
        
        $config = array_merge($defaults, $data);
        
        $methodAttr = $config['method'] ? ' method="' . htmlspecialchars($config['method']) . '"' : '';
        $actionAttr = $config['action'] ? ' action="' . htmlspecialchars($config['action']) . '"' : '';
        $customClass = $config['class'] ? ' ' . htmlspecialchars($config['class']) : '';
        
        $attributes = '';
        if (!empty($config['attributes'])) {
            foreach ($config['attributes'] as $attr => $value) {
                $attributes .= ' ' . htmlspecialchars($attr) . '="' . htmlspecialchars($value) . '"';
            }
        }
        
        ob_start();
        ?>
        <form class="zhazhasu-tech-form<?php echo $customClass; ?>"<?php echo $methodAttr . $actionAttr . $attributes; ?>>
            <?php foreach ($config['fields'] as $field): ?>
            <?php echo self::renderFormField($field); ?>
            <?php endforeach; ?>
        </form>
        <?php
        return ob_get_clean();
    }
    
    /**
     * 渲染表单字段
     */
    private static function renderFormField($field) {
        $defaults = [
            'type' => 'text',
            'name' => '',
            'label' => '',
            'placeholder' => '',
            'value' => '',
            'required' => false,
            'disabled' => false,
            'readonly' => false,
            'options' => [],
            'class' => '',
            'attributes' => []
        ];
        
        $config = array_merge($defaults, $field);
        
        $id = $config['name'] ? ' id="zhazhasu-tech-field-' . htmlspecialchars($config['name']) . '"' : '';
        $nameAttr = $config['name'] ? ' name="' . htmlspecialchars($config['name']) . '"' : '';
        $placeholderAttr = $config['placeholder'] ? ' placeholder="' . htmlspecialchars($config['placeholder']) . '"' : '';
        $valueAttr = $config['value'] ? ' value="' . htmlspecialchars($config['value']) . '"' : '';
        $requiredAttr = $config['required'] ? ' required' : '';
        $disabledAttr = $config['disabled'] ? ' disabled' : '';
        $readonlyAttr = $config['readonly'] ? ' readonly' : '';
        $customClass = $config['class'] ? ' ' . htmlspecialchars($config['class']) : '';
        
        $attributes = '';
        if (!empty($config['attributes'])) {
            foreach ($config['attributes'] as $attr => $value) {
                $attributes .= ' ' . htmlspecialchars($attr) . '="' . htmlspecialchars($value) . '"';
            }
        }
        
        ob_start();
        ?>
        <div class="zhazhasu-tech-form-group">
            <?php if ($config['label']): ?>
            <label class="zhazhasu-tech-form-label"<?php echo $id; ?>>
                <?php echo htmlspecialchars($config['label']); ?>
                <?php if ($config['required']): ?>
                <span class="required">*</span>
                <?php endif; ?>
            </label>
            <?php endif; ?>
            
            <?php if ($config['type'] === 'select'): ?>
            <select class="zhazhasu-tech-form-select<?php echo $customClass; ?>"<?php echo $id . $nameAttr . $requiredAttr . $disabledAttr . $attributes; ?>>
                <?php foreach ($config['options'] as $option): ?>
                <option value="<?php echo htmlspecialchars($option['value']); ?>" <?php echo $option['value'] == $config['value'] ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($option['label']); ?>
                </option>
                <?php endforeach; ?>
            </select>
            
            <?php elseif ($config['type'] === 'textarea'): ?>
            <textarea class="zhazhasu-tech-form-textarea<?php echo $customClass; ?>"<?php echo $id . $nameAttr . $placeholderAttr . $requiredAttr . $disabledAttr . $readonlyAttr . $attributes; ?>><?php echo htmlspecialchars($config['value']); ?></textarea>
            
            <?php else: ?>
            <input type="<?php echo htmlspecialchars($config['type']); ?>" class="zhazhasu-tech-form-input<?php echo $customClass; ?>"<?php echo $id . $nameAttr . $placeholderAttr . $valueAttr . $requiredAttr . $disabledAttr . $readonlyAttr . $attributes; ?>>
            <?php endif; ?>
        </div>
        <?php
        return ob_get_clean();
    }
    
    /**
     * 渲染徽章组件
     */
    public static function renderBadge($data = []) {
        $defaults = [
            'text' => '徽章',
            'type' => 'primary', // primary, secondary, success, warning, danger, info
            'size' => 'medium', // small, medium, large
            'pill' => false,
            'outline' => false,
            'class' => '',
            'attributes' => []
        ];
        
        $config = array_merge($defaults, $data);
        
        $sizeClass = $config['size'] !== 'medium' ? ' zhazhasu-tech-badge-' . $config['size'] : '';
        $pillClass = $config['pill'] ? ' zhazhasu-tech-badge-pill' : '';
        $outlineClass = $config['outline'] ? ' zhazhasu-tech-badge-outline' : '';
        $customClass = $config['class'] ? ' ' . htmlspecialchars($config['class']) : '';
        
        $attributes = '';
        if (!empty($config['attributes'])) {
            foreach ($config['attributes'] as $attr => $value) {
                $attributes .= ' ' . htmlspecialchars($attr) . '="' . htmlspecialchars($value) . '"';
            }
        }
        
        ob_start();
        ?>
        <span class="zhazhasu-tech-badge zhazhasu-tech-badge-<?php echo htmlspecialchars($config['type']); ?><?php echo $sizeClass . $pillClass . $outlineClass . $customClass; ?>"<?php echo $attributes; ?>>
            <?php echo htmlspecialchars($config['text']); ?>
        </span>
        <?php
        return ob_get_clean();
    }
    
    /**
     * 渲染进度条组件
     */
    public static function renderProgressBar($data = []) {
        $defaults = [
            'value' => 0,
            'max' => 100,
            'type' => 'primary', // primary, success, warning, danger, info
            'size' => 'medium', // small, medium, large
            'striped' => false,
            'animated' => false,
            'showLabel' => false,
            'class' => '',
            'attributes' => []
        ];
        
        $config = array_merge($defaults, $data);
        
        $percentage = min(100, max(0, ($config['value'] / $config['max']) * 100));
        $sizeClass = $config['size'] !== 'medium' ? ' zhazhasu-tech-progress-' . $config['size'] : '';
        $stripedClass = $config['striped'] ? ' zhazhasu-tech-progress-striped' : '';
        $animatedClass = $config['animated'] ? ' zhazhasu-tech-progress-animated' : '';
        $customClass = $config['class'] ? ' ' . htmlspecialchars($config['class']) : '';
        
        $attributes = '';
        if (!empty($config['attributes'])) {
            foreach ($config['attributes'] as $attr => $value) {
                $attributes .= ' ' . htmlspecialchars($attr) . '="' . htmlspecialchars($value) . '"';
            }
        }
        
        ob_start();
        ?>
        <div class="zhazhasu-tech-progress<?php echo $sizeClass . $stripedClass . $animatedClass . $customClass; ?>"<?php echo $attributes; ?>>
            <div class="zhazhasu-tech-progress-bar zhazhasu-tech-progress-bar-<?php echo htmlspecialchars($config['type']); ?>" style="width: <?php echo $percentage; ?>%;" role="progressbar" aria-valuenow="<?php echo $config['value']; ?>" aria-valuemin="0" aria-valuemax="<?php echo $config['max']; ?>">
                <?php if ($config['showLabel']): ?>
                <span class="zhazhasu-tech-progress-label"><?php echo round($percentage); ?>%</span>
                <?php endif; ?>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
    
    /**
     * 快速渲染方法 - 卡片布局
     */
    public static function renderCardLayout($cards) {
        $html = '<div class="zhazhasu-tech-card-grid">';
        
        foreach ($cards as $card) {
            $html .= self::renderCard($card);
        }
        
        $html .= '</div>';
        
        return $html;
    }
    
    /**
     * 快速渲染方法 - 按钮组
     */
    public static function renderButtonGroup($buttons) {
        $html = '<div class="zhazhasu-tech-button-group">';
        
        foreach ($buttons as $button) {
            $html .= self::renderButton($button);
        }
        
        $html .= '</div>';
        
        return $html;
    }
    
    /**
     * 获取组件版本信息
     */
    public static function getVersion() {
        return self::VERSION;
    }
    
    /**
     * 获取配置信息
     */
    public static function getConfig() {
        return self::$config;
    }

    /**
     * 获取基础路径
     *
     * @param string $commonBasePath 公共组件基础路径
     * @return string 基础路径
     */
    private static function getBasePath($commonBasePath = null) {
        // 如果传入了公共组件基础路径，使用它
        if ($commonBasePath !== null) {
            return $commonBasePath;
        }

        // 尝试使用全局变量
        if (isset($GLOBALS['commonBasePath'])) {
            return $GLOBALS['commonBasePath'];
        }

        // 尝试使用局部变量
        if (isset($commonBasePath)) {
            return $commonBasePath;
        }

        // 默认相对路径
        return '../../../common/';
    }
}

// 辅助函数：快速渲染组件
if (!function_exists('zhazhasu_tech_card')) {
    function zhazhasu_tech_card($data = []) {
        return Zhazhasu_TechBlue::renderCard($data);
    }
}

if (!function_exists('zhazhasu_tech_button')) {
    function zhazhasu_tech_button($data = []) {
        return Zhazhasu_TechBlue::renderButton($data);
    }
}

if (!function_exists('zhazhasu_tech_alert')) {
    function zhazhasu_tech_alert($data = []) {
        return Zhazhasu_TechBlue::renderAlert($data);
    }
}

if (!function_exists('zhazhasu_tech_modal')) {
    function zhazhasu_tech_modal($data = []) {
        return Zhazhasu_TechBlue::renderModal($data);
    }
}

/**
 * Zhazhasu炸炸酥网安靱场团队 - 科技蓝UI组件库 - 可折叠组件扩展
 */
class Zhazhasu_TechBlue_Collapse {

    /**
     * 渲染可折叠区域组件
     */
    public static function renderCollapse($data = []) {
        $defaults = [
            'items' => [],
            'accordion' => false, // 是否启用手风琴模式
            'defaultOpen' => null, // 默认打开的项目索引（从0开始）
            'class' => '',
            'id' => '',
            'attributes' => []
        ];

        $config = array_merge($defaults, $data);

        if (empty($config['items'])) {
            return '';
        }

        $accordionClass = $config['accordion'] ? ' zhazhasu-tech-collapse-accordion' : '';
        $customClass = $config['class'] ? ' ' . htmlspecialchars($config['class']) : '';
        $id = $config['id'] ? ' id="' . htmlspecialchars($config['id']) . '"' : '';

        $attributes = '';
        if (!empty($config['attributes'])) {
            foreach ($config['attributes'] as $attr => $value) {
                $attributes .= ' ' . htmlspecialchars($attr) . '="' . htmlspecialchars($value) . '"';
            }
        }

        ob_start();
        ?>
        <div class="zhazhasu-tech-collapse<?php echo $accordionClass . $customClass; ?>"<?php echo $id . $attributes; ?>>
            <?php foreach ($config['items'] as $index => $item): ?>
                <?php
                $itemDefaults = [
                    'title' => '',
                    'content' => '',
                    'icon' => '',
                    'open' => false,
                    'disabled' => false,
                    'class' => '',
                    'attributes' => []
                ];
                $itemConfig = array_merge($itemDefaults, $item);

                $activeClass = ($itemConfig['open'] || $config['defaultOpen'] === $index) ? ' active' : '';
                $disabledClass = $itemConfig['disabled'] ? ' disabled' : '';
                $itemCustomClass = $itemConfig['class'] ? ' ' . htmlspecialchars($itemConfig['class']) : '';

                $itemAttributes = '';
                if (!empty($itemConfig['attributes'])) {
                    foreach ($itemConfig['attributes'] as $attr => $value) {
                        $itemAttributes .= ' ' . htmlspecialchars($attr) . '="' . htmlspecialchars($value) . '"';
                    }
                }
                ?>
                <div class="zhazhasu-tech-collapse-item<?php echo $activeClass . $disabledClass . $itemCustomClass; ?>"<?php echo $itemAttributes; ?>>
                    <div class="zhazhasu-tech-collapse-header"<?php echo $itemConfig['disabled'] ? ' tabindex="-1"' : ''; ?>>
                        <div class="zhazhasu-tech-collapse-title">
                            <?php if ($itemConfig['icon']): ?>
                                <i class="<?php echo htmlspecialchars($itemConfig['icon']); ?>"></i>
                            <?php endif; ?>
                            <?php echo htmlspecialchars($itemConfig['title']); ?>
                        </div>
                        <i class="fa fa-chevron-down zhazhasu-tech-collapse-arrow"></i>
                    </div>
                    <div class="zhazhasu-tech-collapse-content">
                        <div class="zhazhasu-tech-collapse-body">
                            <?php echo $itemConfig['content']; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * 渲染单个可折叠项（用于动态添加）
     */
    public static function renderCollapseItem($data = []) {
        $defaults = [
            'title' => '',
            'content' => '',
            'icon' => '',
            'open' => false,
            'disabled' => false,
            'class' => '',
            'attributes' => []
        ];

        $config = array_merge($defaults, $data);

        $activeClass = $config['open'] ? ' active' : '';
        $disabledClass = $config['disabled'] ? ' disabled' : '';
        $customClass = $config['class'] ? ' ' . htmlspecialchars($config['class']) : '';

        $attributes = '';
        if (!empty($config['attributes'])) {
            foreach ($config['attributes'] as $attr => $value) {
                $attributes .= ' ' . htmlspecialchars($attr) . '="' . htmlspecialchars($value) . '"';
            }
        }

        ob_start();
        ?>
        <div class="zhazhasu-tech-collapse-item<?php echo $activeClass . $disabledClass . $customClass; ?>"<?php echo $attributes; ?>>
            <div class="zhazhasu-tech-collapse-header"<?php echo $config['disabled'] ? ' tabindex="-1"' : ''; ?>>
                <div class="zhazhasu-tech-collapse-title">
                    <?php if ($config['icon']): ?>
                        <i class="<?php echo htmlspecialchars($config['icon']); ?>"></i>
                    <?php endif; ?>
                    <?php echo htmlspecialchars($config['title']); ?>
                </div>
                <i class="fa fa-chevron-down zhazhasu-tech-collapse-arrow"></i>
            </div>
            <div class="zhazhasu-tech-collapse-content">
                <div class="zhazhasu-tech-collapse-body">
                    <?php echo $config['content']; ?>
                </div>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
}

if (!function_exists('zhazhasu_tech_card')) {
    function zhazhasu_tech_card($data = []) {
        return Zhazhasu_TechBlue::renderCard($data);
    }
}

if (!function_exists('zhazhasu_tech_button')) {
    function zhazhasu_tech_button($data = []) {
        return Zhazhasu_TechBlue::renderButton($data);
    }
}

if (!function_exists('zhazhasu_tech_alert')) {
    function zhazhasu_tech_alert($data = []) {
        return Zhazhasu_TechBlue::renderAlert($data);
    }
}

if (!function_exists('zhazhasu_tech_modal')) {
    function zhazhasu_tech_modal($data = []) {
        return Zhazhasu_TechBlue::renderModal($data);
    }
}

if (!function_exists('zhazhasu_tech_form')) {
    function zhazhasu_tech_form($data = []) {
        return Zhazhasu_TechBlue::renderForm($data);
    }
}

if (!function_exists('zhazhasu_tech_table')) {
    function zhazhasu_tech_table($data = []) {
        return Zhazhasu_TechBlue::renderTable($data);
    }
}

if (!function_exists('zhazhasu_tech_badge')) {
    function zhazhasu_tech_badge($data = []) {
        return Zhazhasu_TechBlue::renderBadge($data);
    }
}

if (!function_exists('zhazhasu_tech_progress')) {
    function zhazhasu_tech_progress($data = []) {
        return Zhazhasu_TechBlue::renderProgressBar($data);
    }
}

if (!function_exists('zhazhasu_tech_collapse')) {
    function zhazhasu_tech_collapse($data = []) {
        return Zhazhasu_TechBlue_Collapse::renderCollapse($data);
    }
}

if (!function_exists('zhazhasu_tech_collapse_item')) {
    function zhazhasu_tech_collapse_item($data = []) {
        return Zhazhasu_TechBlue_Collapse::renderCollapseItem($data);
    }
}