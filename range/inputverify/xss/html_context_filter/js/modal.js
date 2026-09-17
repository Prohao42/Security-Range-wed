/**
 * Zhazhasu炸炸酥网安靱场团队 - HTML上下文XSS过滤靶场 - 模态框组件
 * 版本: v1.0.0
 * 创建日期: 2026-01-14
 * 团队: 炸炸酥网安靱场 (Zhazhasu)
 */

var ZhazhasuModal = (function() {
    'use strict';

    /**
     * 创建模态框
     */
    function createModal() {
        var modal = document.createElement('div');
        modal.id = 'zhazhasu-modal';
        modal.className = 'zhazhasu-modal';
        modal.innerHTML =
            '<div class="zhazhasu-modal-content">' +
                '<div class="zhazhasu-modal-header">' +
                    '<h3 id="zhazhasu-modal-title"></h3>' +
                    '<button class="zhazhasu-modal-close" onclick="ZhazhasuModal.hide()">&times;</button>' +
                '</div>' +
                '<div class="zhazhasu-modal-body" id="zhazhasu-modal-body"></div>' +
            '</div>';
        document.body.appendChild(modal);
    }

    /**
     * 显示模态框
     */
    function show(title, message) {
        var modal = document.getElementById('zhazhasu-modal');
        if (!modal) {
            createModal();
            modal = document.getElementById('zhazhasu-modal');
        }

        document.getElementById('zhazhasu-modal-title').textContent = title;
        document.getElementById('zhazhasu-modal-body').textContent = message;
        modal.style.display = 'block';
    }

    /**
     * 隐藏模态框
     */
    function hide() {
        var modal = document.getElementById('zhazhasu-modal');
        if (modal) {
            modal.style.display = 'none';
        }
    }

    /**
     * 显示错误消息
     */
    function showError(title, message) {
        show(title, message);
        var modal = document.querySelector('.zhazhasu-modal-content');
        if (modal) {
            modal.style.borderTop = '4px solid #dc3545';
        }
    }

    /**
     * 显示成功消息
     */
    function showSuccess(title, message) {
        show(title, message);
        var modal = document.querySelector('.zhazhasu-modal-content');
        if (modal) {
            modal.style.borderTop = '4px solid #28a745';
        }
    }

    return {
        show: show,
        hide: hide,
        showError: showError,
        showSuccess: showSuccess
    };

})();

// 点击模态框外部关闭
window.onclick = function(event) {
    var modal = document.getElementById('zhazhasu-modal');
    if (modal && event.target === modal) {
        ZhazhasuModal.hide();
    }
};
