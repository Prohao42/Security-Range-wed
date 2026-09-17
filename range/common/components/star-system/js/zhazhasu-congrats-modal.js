/**
 * Zhazhasu炸炸酥网安靱场团队 - 华丽恭喜消息组件
 * Luxury Congratulations Modal Component
 * 版本: v1.0.0
 * 创建日期: 2025-11-08
 * 团队: 炸炸酥网安靱场 (Zhazhasu)
 *
 * 专为成就解锁设计的华丽恭喜消息组件
 */

class ZhazhasuCongratsModal {
    constructor(options = {}) {
        // 默认配置
        this.config = {
            title: '🎉 恭喜！',
            message: '所有成就已解锁！',
            buttonText: '继续学习',
            autoClose: false,
            showParticles: true,
            particleCount: 8,
            animationDuration: 2000,
            onClose: null,
            onShow: null,
            // 新增配置
            enableNextRangeButton: false,
            rangeCode: '',
            nextRangeApiUrl: '',
            updateLearningStatus: true,     // 自动更新学习状态
            updateStatusApiUrl: '',
            fallbackButtonText: '返回首页',
            fallbackUrl: '/',
            buttons: [],
            ...options
        };

        // 状态管理
        this.isVisible = false;
        this.isAnimating = false;
        this.modalElement = null;
        this.particles = [];
        this.nextRangeData = null;

        // 确保只创建一个实例
        if (!ZhazhasuCongratsModal.instance) {
            this.init();
            ZhazhasuCongratsModal.instance = this;
        }

        return ZhazhasuCongratsModal.instance;
    }

    /**
     * 初始化组件
     */
    init() {
        this.createModal();
        this.bindEvents();
        // [Zhazhasu Log Cleanup - 2025-11-22]
        // console.log('[Zhazhasu] 恭喜消息组件已初始化', this.config);
    }

    /**
     * 创建弹窗元素
     */
    createModal() {
        // 构建按钮HTML
        let buttonsHtml = '';

        if (this.config.buttons && this.config.buttons.length > 0) {
            // 使用自定义按钮
            this.config.buttons.forEach((button, index) => {
                buttonsHtml += `<button class="zhazhasu-congrats-button ${button.class || ''}" data-action="${button.action || 'close'}" data-index="${index}">
                    ${button.text || '按钮'}
                </button>`;
            });
        } else if (this.config.enableNextRangeButton) {
            // 启用下一靶场按钮，先创建占位符，稍后填充
            buttonsHtml = `
                <button class="zhazhasu-congrats-button" id="zhazhasu-congrats-close">
                    ${this.config.buttonText}
                </button>
                <button class="zhazhasu-congrats-button zhazhasu-congrats-next-button" id="zhazhasu-congrats-next" style="display: none;">
                    正在获取...
                </button>
            `;
        } else {
            // 默认按钮
            buttonsHtml = `
                <button class="zhazhasu-congrats-button" id="zhazhasu-congrats-close">
                    ${this.config.buttonText}
                </button>
            `;
        }

        const modalHtml = `
            <div class="zhazhasu-congrats-modal" id="zhazhasu-congrats-modal">
                <div class="zhazhasu-congrats-overlay"></div>
                <div class="zhazhasu-congrats-container">
                    <div class="zhazhasu-congrats-particles"></div>
                    <div class="zhazhasu-congrats-title">
                        <h2>${this.config.title}</h2>
                    </div>
                    <div class="zhazhasu-congrats-content">
                        <p class="zhazhasu-congrats-message">${this.config.message}</p>
                    </div>
                    <div class="zhazhasu-congrats-actions">
                        ${buttonsHtml}
                    </div>
                </div>
            </div>
        `;

        document.body.insertAdjacentHTML('beforeend', modalHtml);
        this.modalElement = document.getElementById('zhazhasu-congrats-modal');
        this.containerElement = this.modalElement.querySelector('.zhazhasu-congrats-container');
        this.particlesContainer = this.modalElement.querySelector('.zhazhasu-congrats-particles');

        // 创建粒子
        if (this.config.showParticles) {
            this.createParticles();
        }
    }

    /**
     * 创建粒子效果
     */
    createParticles() {
        for (let i = 0; i < this.config.particleCount; i++) {
            const particle = document.createElement('div');
            particle.className = 'congrats-particle';
            particle.style.left = '50%';
            particle.style.top = '50%';
            this.particlesContainer.appendChild(particle);
            this.particles.push(particle);
        }
    }

    /**
     * 绑定事件
     */
    bindEvents() {
        // 遮罩层点击事件
        const overlay = this.modalElement.querySelector('.zhazhasu-congrats-overlay');
        /* [Zhazhasu Update] 禁用点击遮罩层关闭模态框
        overlay.addEventListener('click', (e) => {
            e.preventDefault();
            this.hide();
        });
        */

        // 绑定所有按钮事件
        this.bindButtonEvents();

        // ESC键关闭
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && this.isVisible) {
                this.hide();
            }
        });
    }

    /**
     * 绑定按钮事件
     */
    bindButtonEvents() {
        const buttons = this.modalElement.querySelectorAll('.zhazhasu-congrats-button');

        buttons.forEach(button => {
            button.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation(); // 阻止事件冒泡

                // [Zhazhasu Log Cleanup - 2025-11-22]
                // console.log('[Zhazhasu] 按钮被点击:', button.id, button.textContent);

                // 根据按钮ID处理，避免action属性问题
                if (button.id === 'zhazhasu-congrats-close') {
                    this.handleCloseClick();
                } else if (button.id === 'zhazhasu-congrats-next') {
                    this.handleNextClick();
                } else {
                    // 使用action属性处理自定义按钮
                    const action = button.dataset.action || 'close';
                    const index = button.dataset.index;

                    switch (action) {
                        case 'close':
                            this.handleCloseClick();
                            break;
                        case 'custom':
                            this.handleCustomClick(index, button);
                            break;
                        case 'next':
                            this.handleNextClick();
                            break;
                        default:
                            this.handleCloseClick();
                            break;
                    }
                }
            });
        });
    }

    /**
     * 处理关闭按钮点击
     */
    handleCloseClick() {
        if (this.config.onClose) {
            this.config.onClose();
        }
        this.hide();
    }

    /**
     * 处理自定义按钮点击
     */
    handleCustomClick(index, button) {
        if (this.config.buttons && this.config.buttons[index]) {
            const buttonConfig = this.config.buttons[index];
            if (buttonConfig.onClick) {
                buttonConfig.onClick(buttonConfig, this);
            }

            if (buttonConfig.url) {
                window.location.href = buttonConfig.url;
            }
        }
    }

    /**
     * 处理下一靶场按钮点击
     */
    handleNextClick() {
        // [Zhazhasu Log Cleanup - 2025-11-22]
        // console.log('[Zhazhasu] 下一靶场按钮被点击');
        // console.log('[Zhazhasu] nextRangeData:', this.nextRangeData);

        if (this.nextRangeData && this.nextRangeData.url) {
            let url = this.nextRangeData.url;
            // [Zhazhasu Log Cleanup - 2025-11-22]
            // console.log('[Zhazhasu] 跳转URL:', url);
            // console.log('[Zhazhasu] 当前页面:', window.location.href);

            // 直接使用相对路径跳转，简化处理逻辑
            window.location.href = url;
        } else {
            // [Zhazhasu Log Cleanup - 2025-11-22]
            // console.warn('[Zhazhasu] 下一靶场数据未准备好', this.nextRangeData);
        }
    }

    /**
     * 显示弹窗
     */
    show(options = {}) {
        // 更新配置
        if (options) {
            this.config = { ...this.config, ...options };
            this.updateContent();
        }

        if (this.isVisible || this.isAnimating) {
            return false;
        }

        this.isAnimating = true;
        this.modalElement.classList.add('show');
        this.isVisible = true;

        // 触发显示回调
        if (this.config.onShow) {
            this.config.onShow();
        }

        // 触发自定义事件
        this.triggerEvent('congratsShow', { config: this.config });

        // 如果启用下一靶场按钮，获取下一靶场信息
        if (this.config.enableNextRangeButton) {
            this.fetchNextRangeInfo();
        }

        // 如果启用学习状态更新，自动更新为已掌握
        if (this.config.updateLearningStatus) {
            this.updateLearningStatus();
        }

        setTimeout(() => {
            this.isAnimating = false;
        }, parseFloat(getComputedStyle(document.documentElement)
            .getPropertyValue('--congrats-entrance-duration')) * 1000);

        // 自动关闭
        if (this.config.autoClose && this.config.autoClose > 0) {
            setTimeout(() => {
                this.hide();
            }, this.config.autoClose);
        }

        return true;
    }

    /**
     * 隐藏弹窗
     */
    hide() {
        if (!this.isVisible || this.isAnimating) {
            return false;
        }

        this.isAnimating = true;
        this.modalElement.classList.add('closing');
        this.isVisible = false;

        // 触发关闭回调
        if (this.config.onClose) {
            this.config.onClose();
        }

        // 触发自定义事件
        this.triggerEvent('congratsHide', { config: this.config });

        setTimeout(() => {
            this.modalElement.classList.remove('show', 'closing');
            this.isAnimating = false;
        }, parseFloat(getComputedStyle(document.documentElement)
            .getPropertyValue('--congrats-exit-duration')) * 1000);

        return true;
    }

    /**
     * 更新内容
     */
    updateContent() {
        const titleElement = this.modalElement.querySelector('.zhazhasu-congrats-title h2');
        const messageElement = this.modalElement.querySelector('.zhazhasu-congrats-message');
        const buttonElement = this.modalElement.querySelector('#zhazhasu-congrats-close');

        if (titleElement) {
            // 将emoji字符包装在span中，保持原有颜色
            titleElement.innerHTML = this.wrapEmojis(this.config.title);
        }
        if (messageElement) messageElement.textContent = this.config.message;
        if (buttonElement) buttonElement.textContent = this.config.buttonText;
    }

    /**
     * 包装emoji字符，保持原有颜色
     */
    wrapEmojis(text) {
        if (!text) return '';

        // 匹配emoji字符的正则表达式
        const emojiRegex = /[\u{1F600}-\u{1F64F}]|[\u{1F300}-\u{1F5FF}]|[\u{1F680}-\u{1F6FF}]|[\u{1F1E0}-\u{1F1FF}]|[\u{2600}-\u{26FF}]|[\u{2700}-\u{27BF}]/gu;

        return text.replace(emojiRegex, (match) => {
            return `<span class="emoji-preserver">${match}</span>`;
        });
    }

    /**
     * 更新配置
     */
    updateConfig(newConfig) {
        this.config = { ...this.config, ...newConfig };
        this.updateContent();
    }

    /**
     * 触发自定义事件
     */
    triggerEvent(eventName, data) {
        const event = new CustomEvent(`zhazhasu:${eventName}`, {
            detail: data,
            bubbles: true,
            cancelable: true
        });
        this.modalElement.dispatchEvent(event);
    }

    /**
     * 销毁组件
     */
    destroy() {
        if (this.modalElement && this.modalElement.parentNode) {
            this.modalElement.parentNode.removeChild(this.modalElement);
        }
        this.particles = [];
        this.modalElement = null;
        this.containerElement = null;
        this.particlesContainer = null;
        ZhazhasuCongratsModal.instance = null;
    }

    /**
     * 检查是否显示中
     */
    isShowing() {
        return this.isVisible;
    }

    /**
     * 获取当前配置
     */
    getConfig() {
        return { ...this.config };
    }

    // 静态方法
    /**
     * 快速显示
     */
    static show(options = {}) {
        if (!ZhazhasuCongratsModal.instance) {
            new ZhazhasuCongratsModal(options);
        }
        return ZhazhasuCongratsModal.instance.show(options);
    }

    /**
     * 快速隐藏
     */
    static hide() {
        if (ZhazhasuCongratsModal.instance) {
            return ZhazhasuCongratsModal.instance.hide();
        }
        return false;
    }

    /**
     * 获取实例
     */
    static getInstance() {
        return ZhazhasuCongratsModal.instance;
    }

    /**
     * 销毁实例
     */
    static destroy() {
        if (ZhazhasuCongratsModal.instance) {
            ZhazhasuCongratsModal.instance.destroy();
        }
    }

    /**
     * 获取下一靶场信息
     */
    async fetchNextRangeInfo() {
        const nextButton = this.modalElement.querySelector('#zhazhasu-congrats-next');

        if (!nextButton) {
            return;
        }

        // 如果没有提供靶场代码，尝试从当前路径自动检测
        let rangeCode = this.config.rangeCode;
        if (!rangeCode) {
            rangeCode = this.detectRangeCodeFromPath();
        }

        if (!rangeCode) {
            // [Zhazhasu Log Cleanup - 2025-11-22]
            // console.warn('[Zhazhasu] 无法确定当前靶场代码');
            nextButton.style.display = 'none';
            return;
        }

        // 显示加载状态
        nextButton.textContent = '正在获取...';
        nextButton.style.display = 'inline-block';

        try {
            const response = await fetch(this.config.nextRangeApiUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    code: rangeCode
                })
            });

            const data = await response.json();

            if (data.success && data.data && data.data.next_range) {
                this.nextRangeData = data.data.next_range;

                // 更新按钮
                nextButton.textContent = this.nextRangeData.title || '下一个靶场';
                nextButton.style.display = 'inline-block';

                // [Zhazhasu Log Cleanup - 2025-11-22]
                // console.log('[Zhazhasu] 下一靶场信息已获取:', this.nextRangeData);
            } else {
                // 没有下一靶场，显示返回首页
                nextButton.textContent = this.config.fallbackButtonText || '返回首页';
                nextButton.style.display = 'inline-block';
                this.nextRangeData = { url: this.config.fallbackUrl || '/' };
                // [Zhazhasu Log Cleanup - 2025-11-22]
                // console.warn('[Zhazhasu] 获取下一靶场信息失败或不存在:');
            }
        } catch (error) {
            // 发生异常，显示返回首页
            nextButton.textContent = this.config.fallbackButtonText || '返回首页';
            nextButton.style.display = 'inline-block';
            this.nextRangeData = { url: this.config.fallbackUrl || '/' };
            // [Zhazhasu Log Cleanup - 2025-11-22]
            // console.error('[Zhazhasu] 获取下一靶场信息时发生错误:', error);
        }
    }

    /**
     * 更新学习状态
     */
    async updateLearningStatus() {
        // 获取靶场代码
        let rangeCode = this.config.rangeCode;
        if (!rangeCode) {
            rangeCode = this.detectRangeCodeFromPath();
        }

        if (!rangeCode) {
            // [Zhazhasu Log Cleanup - 2025-11-22]
            // console.warn('[Zhazhasu] 无法确定当前靶场代码，跳过学习状态更新');
            return;
        }

        // 获取学习状态，优先使用配置中的状态，默认为已掌握
        let learningStatus = this.config.learningStatus || '已掌握';

        // [Zhazhasu Log Cleanup - 2025-11-22]
        // console.log('[Zhazhasu] 开始更新学习状态:', rangeCode, '状态:', learningStatus);

        try {
            const response = await fetch(this.config.updateStatusApiUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    code: rangeCode,
                    status: learningStatus
                })
            });

            const data = await response.json();

            if (data.success) {
                // [Zhazhasu Log Cleanup - 2025-11-22]
                // console.log('[Zhazhasu] 学习状态更新成功:', data.message);
                this.triggerEvent('learningStatusUpdated', {
                    rangeCode: rangeCode,
                    status: learningStatus,
                    response: data.data
                });
            } else {
                // [Zhazhasu Log Cleanup - 2025-11-22]
                // console.warn('[Zhazhasu] 学习状态更新失败:', data.message);
            }
        } catch (error) {
            // [Zhazhasu Log Cleanup - 2025-11-22]
            // console.error('[Zhazhasu] 更新学习状态时发生错误:', error);
        }
    }

    /**
     * 从当前路径检测靶场代码
     */
    detectRangeCodeFromPath() {
        const path = window.location.pathname;

        // 从路径中提取靶场代码
        // 支持多种路径格式：
        // 1. /zhazhasudev/range/logic/brokenac/idref/ -> idref
        // 2. /zhazhasudev/range/base/http/httpal/index.php -> httpal
        // 3. /range/logic/brokenac/idref/ -> idref

        // 先尝试匹配带多级目录的格式 (range/一级分类/二级分类/靶场代码/)
        let match = path.match(/\/range\/[^\/]+\/[^\/]+\/([^\/]+)(?:\/|$)/);
        if (match && match[1]) {
            return match[1];
        }

        // 再尝试匹配简单的两级格式 (range/分类/靶场代码/)
        match = path.match(/\/range\/[^\/]+\/([^\/]+)(?:\/|$)/);
        if (match && match[1]) {
            return match[1];
        }

        return '';
    }
}

// 自动初始化支持
document.addEventListener('DOMContentLoaded', function () {
    // 检查是否有自动初始化的标记
    const autoElements = document.querySelectorAll('[data-zhazhasu-congrats-auto]');
    if (autoElements.length > 0) {
        new ZhazhasuCongratsModal();
    }
});

// 导出到全局对象
if (typeof window !== 'undefined') {
    if (!window.Zhazhasu) {
        window.Zhazhasu = {};
    }
    window.Zhazhasu.CongratsModal = ZhazhasuCongratsModal;
    // 同时导出为全局变量，方便直接调用
    window.ZhazhasuCongratsModal = ZhazhasuCongratsModal;
}

// 兼容不同的模块系统
if (typeof module !== 'undefined' && module.exports) {
    module.exports = ZhazhasuCongratsModal;
}

// [Zhazhasu Log Cleanup - 2025-11-22]
// console.log('[Zhazhasu] 华丽恭喜消息组件已加载 - 炸炸酥网安靱场团队');