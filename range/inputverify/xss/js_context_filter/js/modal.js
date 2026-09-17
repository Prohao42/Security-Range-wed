/**
 * Zhazhasu 自定义模态框组件
 * 版本: v1.0.0
 * 创建日期: 2025-01-29
 * 团队: 炸炸酥网安靱场 (Zhazhasu)
 *
 * 说明: 用于JavaScript上下文XSS过滤靶场的错误提示和消息显示
 */

(function() {
    'use strict';

    /**
     * 模态框类
     */
    var ZhazhasuModal = function() {
        this.modalElement = null;
        this.overlayElement = null;
        this.isVisible = false;
        this.config = {
            title: '',
            message: '',
            type: 'info',  // info, success, error, warning
            closable: true,
            closeOnOverlayClick: true,
            buttons: [],
            onShow: null,
            onClose: null
        };
    };

    /**
     * 显示模态框
     *
     * @param {Object} config 配置对象
     */
    ZhazhasuModal.prototype.show = function(config) {
        // 合并配置
        this.config = this._mergeConfig(config);

        // 创建模态框元素
        this._createModal();

        // 显示模态框
        this._show();

        // 绑定事件
        this._bindEvents();

        // 调用显示回调
        if (this.config.onShow) {
            this.config.onShow();
        }
    };

    /**
     * 隐藏模态框
     */
    ZhazhasuModal.prototype.hide = function() {
        if (!this.isVisible) {
            return;
        }

        var self = this;

        // 添加隐藏动画类
        this.modalElement.classList.add('zhazhasu-modal-hiding');
        this.overlayElement.classList.add('zhazhasu-overlay-hiding');

        // 等待动画结束后移除元素
        setTimeout(function() {
            if (self.modalElement && self.modalElement.parentNode) {
                self.modalElement.parentNode.removeChild(self.modalElement);
            }
            if (self.overlayElement && self.overlayElement.parentNode) {
                self.overlayElement.parentNode.removeChild(self.overlayElement);
            }

            self.isVisible = false;

            // 调用关闭回调
            if (self.config.onClose) {
                self.config.onClose();
            }
        }, 300);
    };

    /**
     * 创建模态框DOM元素
     */
    ZhazhasuModal.prototype._createModal = function() {
        // 如果已存在，先移除
        if (this.modalElement) {
            this.hide();
        }

        // 创建遮罩层
        this.overlayElement = document.createElement('div');
        this.overlayElement.className = 'zhazhasu-modal-overlay';

        // 创建模态框容器
        this.modalElement = document.createElement('div');
        this.modalElement.className = 'zhazhasu-modal-container';

        // 根据类型设置样式类
        var typeClass = 'zhazhasu-modal-' + this.config.type;
        this.modalElement.classList.add(typeClass);

        // 构建模态框HTML
        var html = this._buildModalHTML();
        this.modalElement.innerHTML = html;

        // 添加到页面
        document.body.appendChild(this.overlayElement);
        document.body.appendChild(this.modalElement);
    };

    /**
     * 构建模态框HTML
     */
    ZhazhasuModal.prototype._buildModalHTML = function() {
        var html = '<div class="zhazhasu-modal">';

        // 头部
        html += '<div class="zhazhasu-modal-header">';
        html += '<h3 class="zhazhasu-modal-title">' + this._escapeHtml(this.config.title) + '</h3>';
        if (this.config.closable) {
            html += '<button class="zhazhasu-modal-close" type="button">&times;</button>';
        }
        html += '</div>';

        // 内容
        html += '<div class="zhazhasu-modal-body">';
        html += '<div class="zhazhasu-modal-message">' + this.config.message + '</div>';
        html += '</div>';

        // 按钮
        if (this.config.buttons && this.config.buttons.length > 0) {
            html += '<div class="zhazhasu-modal-footer">';
            for (var i = 0; i < this.config.buttons.length; i++) {
                var btn = this.config.buttons[i];
                var btnClass = 'zhazhasu-modal-btn zhazhasu-modal-btn-' + (btn.type || 'default');
                html += '<button class="' + btnClass + '" data-action="' + btn.action + '">';
                html += this._escapeHtml(btn.text);
                html += '</button>';
            }
            html += '</div>';
        }

        html += '</div>';
        return html;
    };

    /**
     * 显示模态框（带动画）
     */
    ZhazhasuModal.prototype._show = function() {
        var self = this;

        // 先设置为透明，确保元素已添加到DOM
        this.overlayElement.style.opacity = '0';
        this.modalElement.style.opacity = '0';
        this.modalElement.style.transform = 'scale(0.9)';

        // 强制重绘
        this.overlayElement.offsetHeight;
        this.modalElement.offsetHeight;

        // 添加显示动画
        this.overlayElement.style.opacity = '1';
        this.modalElement.style.opacity = '1';
        this.modalElement.style.transform = 'scale(1)';

        this.isVisible = true;

        // 禁止页面滚动
        document.body.style.overflow = 'hidden';
    };

    /**
     * 绑定事件处理器
     */
    ZhazhasuModal.prototype._bindEvents = function() {
        var self = this;

        // 关闭按钮
        if (this.config.closable) {
            var closeBtn = this.modalElement.querySelector('.zhazhasu-modal-close');
            if (closeBtn) {
                closeBtn.addEventListener('click', function() {
                    self.hide();
                });
            }
        }

        // 遮罩层点击关闭
        if (this.config.closeOnOverlayClick) {
            this.overlayElement.addEventListener('click', function() {
                self.hide();
            });
        }

        // 自定义按钮
        var buttons = this.modalElement.querySelectorAll('.zhazhasu-modal-btn');
        for (var i = 0; i < buttons.length; i++) {
            buttons[i].addEventListener('click', function(e) {
                var action = this.getAttribute('data-action');

                // 执行按钮回调
                if (self.config.buttons) {
                    for (var j = 0; j < self.config.buttons.length; j++) {
                        if (self.config.buttons[j].action === action &&
                            typeof self.config.buttons[j].callback === 'function') {
                            self.config.buttons[j].callback();
                            break;
                        }
                    }
                }

                // 默认关闭模态框
                if (action !== 'cancel') {
                    self.hide();
                }
            });
        }

        // ESC键关闭
        this._escapeKeyHandler = function(e) {
            if (e.keyCode === 27 && self.isVisible) {
                self.hide();
            }
        };
        document.addEventListener('keydown', this._escapeKeyHandler);
    };

    /**
     * 合并配置
     */
    ZhazhasuModal.prototype._mergeConfig = function(config) {
        var defaultConfig = {
            title: '提示',
            message: '',
            type: 'info',
            closable: true,
            closeOnOverlayClick: true,
            buttons: [],
            onShow: null,
            onClose: null
        };

        for (var key in config) {
            if (config.hasOwnProperty(key)) {
                defaultConfig[key] = config[key];
            }
        }

        return defaultConfig;
    };

    /**
     * HTML转义
     */
    ZhazhasuModal.prototype._escapeHtml = function(text) {
        var div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    };

    /**
     * 快捷方法：显示错误消息
     */
    ZhazhasuModal.showError = function(title, message, onClose) {
        var modal = new ZhazhasuModal();
        modal.show({
            title: title,
            message: message,
            type: 'error',
            closable: true,
            buttons: [
                {
                    text: '确定',
                    type: 'primary',
                    action: 'ok'
                }
            ],
            onClose: onClose
        });
    };

    /**
     * 快捷方法：显示成功消息
     */
    ZhazhasuModal.showSuccess = function(title, message, onClose) {
        var modal = new ZhazhasuModal();
        modal.show({
            title: title,
            message: message,
            type: 'success',
            closable: true,
            buttons: [
                {
                    text: '确定',
                    type: 'primary',
                    action: 'ok'
                }
            ],
            onClose: onClose
        });
    };

    /**
     * 快捷方法：显示警告消息
     */
    ZhazhasuModal.showWarning = function(title, message, onClose) {
        var modal = new ZhazhasuModal();
        modal.show({
            title: title,
            message: message,
            type: 'warning',
            closable: true,
            buttons: [
                {
                    text: '确定',
                    type: 'primary',
                    action: 'ok'
                }
            ],
            onClose: onClose
        });
    };

    /**
     * 快捷方法：显示信息消息
     */
    ZhazhasuModal.showInfo = function(title, message, onClose) {
        var modal = new ZhazhasuModal();
        modal.show({
            title: title,
            message: message,
            type: 'info',
            closable: true,
            buttons: [
                {
                    text: '确定',
                    type: 'primary',
                    action: 'ok'
                }
            ],
            onClose: onClose
        });
    };

    // 导出到全局
    window.ZhazhasuModal = ZhazhasuModal;

})();
