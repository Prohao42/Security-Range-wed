/**
 * Zhazhasu炸炸酥网安靱场团队 - 文件相关XSS靶场 - 模态框组件
 * 版本: v1.0.0
 * 创建日期: 2026-03-03
 * 团队: 炸炸酥网安靱场 (Zhazhasu)
 */

(function () {
    'use strict';

    /**
     * 模态框管理器
     */
    var ZhazhasuModal = {
        /**
         * 显示错误模态框
         * @param {string} title - 标题
         * @param {string} message - 消息内容
         */
        showError: function (title, message) {
            this._show(title, message, 'error');
        },

        /**
         * 显示成功模态框
         * @param {string} title - 标题
         * @param {string} message - 消息内容
         */
        showSuccess: function (title, message) {
            this._show(title, message, 'success');
        },

        /**
         * 显示警告模态框
         * @param {string} title - 标题
         * @param {string} message - 消息内容
         */
        showWarning: function (title, message) {
            this._show(title, message, 'warning');
        },

        /**
         * 显示信息模态框
         * @param {string} title - 标题
         * @param {string} message - 消息内容
         */
        showInfo: function (title, message) {
            this._show(title, message, 'info');
        },

        /**
         * 显示确认模态框
         * @param {string} title - 标题
         * @param {string} message - 消息内容
         * @param {function} onConfirm - 确认回调
         * @param {function} onCancel - 取消回调
         */
        showConfirm: function (title, message, onConfirm, onCancel) {
            this._show(title, message, 'info', true, onConfirm, onCancel);
        },

        /**
         * 内部显示方法
         */
        _show: function (title, message, type, isConfirm, onConfirm, onCancel) {
            // 移除已存在的模态框
            this._hide();

            // 创建遮罩层
            var overlay = document.createElement('div');
            overlay.className = 'zhazhasu-modal-overlay';
            overlay.id = 'zhazhasu-modal-overlay';

            // 创建模态框容器
            var container = document.createElement('div');
            container.className = 'zhazhasu-modal-container zhazhasu-modal-' + type;
            container.id = 'zhazhasu-modal-container';

            // 模态框内容
            container.innerHTML = '\
                <div class="zhazhasu-modal">\
                    <div class="zhazhasu-modal-header">\
                        <h3 class="zhazhasu-modal-title">' + this._escapeHtml(title) + '</h3>\
                        <button class="zhazhasu-modal-close" onclick="ZhazhasuModal._hide()">&times;</button>\
                    </div>\
                    <div class="zhazhasu-modal-body">\
                        <p class="zhazhasu-modal-message">' + this._escapeHtml(message) + '</p>\
                    </div>\
                    <div class="zhazhasu-modal-footer">\
                        ' + (isConfirm ?
                            '<button class="zhazhasu-modal-btn zhazhasu-modal-btn-default" onclick="ZhazhasuModal._handleCancel()">取消</button>\
                             <button class="zhazhasu-modal-btn zhazhasu-modal-btn-primary" onclick="ZhazhasuModal._handleConfirm()">确定</button>' :
                            '<button class="zhazhasu-modal-btn zhazhasu-modal-btn-primary" onclick="ZhazhasuModal._hide()">确定</button>') + '\
                    </div>\
                </div>';

            // 添加到页面
            document.body.appendChild(overlay);
            document.body.appendChild(container);

            // 保存回调函数
            if (isConfirm) {
                this._onConfirm = onConfirm;
                this._onCancel = onCancel;
            }

            // 绑定遮罩层点击事件
            overlay.onclick = function () {
                if (!isConfirm) {
                    ZhazhasuModal._hide();
                }
            };

            // 绑定ESC键关闭
            document.addEventListener('keydown', this._handleEscKey);
        },

        /**
         * 处理确认按钮
         */
        _handleConfirm: function () {
            var callback = this._onConfirm;
            this._hide();
            if (typeof callback === 'function') {
                callback();
            }
        },

        /**
         * 处理取消按钮
         */
        _handleCancel: function () {
            var callback = this._onCancel;
            this._hide();
            if (typeof callback === 'function') {
                callback();
            }
        },

        /**
         * 处理ESC键
         */
        _handleEscKey: function (e) {
            if (e.key === 'Escape') {
                ZhazhasuModal._hide();
            }
        },

        /**
         * 隐藏模态框
         */
        _hide: function () {
            var overlay = document.getElementById('zhazhasu-modal-overlay');
            var container = document.getElementById('zhazhasu-modal-container');

            if (overlay) {
                overlay.classList.add('zhazhasu-overlay-hiding');
                setTimeout(function () {
                    if (overlay.parentNode) {
                        overlay.parentNode.removeChild(overlay);
                    }
                }, 300);
            }

            if (container) {
                container.classList.add('zhazhasu-modal-hiding');
                setTimeout(function () {
                    if (container.parentNode) {
                        container.parentNode.removeChild(container);
                    }
                }, 300);
            }

            // 移除ESC键监听
            document.removeEventListener('keydown', this._handleEscKey);

            // 清除回调函数
            this._onConfirm = null;
            this._onCancel = null;
        },

        /**
         * HTML转义
         */
        _escapeHtml: function (text) {
            var div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
    };

    // 暴露到全局
    window.ZhazhasuModal = ZhazhasuModal;

    console.log('[Zhazhasu FileXSS] 模态框组件已加载');
})();
