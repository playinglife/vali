const CONTAINER_ID = 'dc-notifications-root';
const STYLE_MARKER = 'data-dc-notifications-styles';

function ensureNotificationStyles() {
    if (document.head.querySelector(`[${STYLE_MARKER}]`)) {
        return;
    }
    const style = document.createElement('style');
    style.setAttribute(STYLE_MARKER, '1');
    style.textContent = `
        #${CONTAINER_ID} {
            position: fixed;
            top: 0.75rem;
            right: 0.75rem;
            left: 0.75rem;
            z-index: 100001;
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            gap: 0.5rem;
            pointer-events: none;
            max-width: 22rem;
            margin-left: auto;
            box-sizing: border-box;
        }
        .dc-notifications__toast {
            pointer-events: auto;
            max-width: 100%;
            padding: 0.65rem 0.85rem;
            border-radius: 8px;
            font-size: 0.8125rem;
            line-height: 1.4;
            box-shadow: 0 6px 24px rgba(0, 0, 0, 0.35);
            border: 1px solid rgba(255, 255, 255, 0.12);
            cursor: pointer;
            opacity: 0;
            transform: translateX(0.5rem);
            transition: opacity 0.2s ease, transform 0.2s ease;
        }
        .dc-notifications__toast--visible {
            opacity: 1;
            transform: translateX(0);
        }
        .dc-notifications__toast--success {
            background: #0d3d24;
            color: #c8f5d9;
            border-color: rgba(80, 200, 120, 0.35);
        }
        .dc-notifications__toast--warning {
            background: #3d320d;
            color: #f5e6c8;
            border-color: rgba(220, 180, 60, 0.45);
        }
        .dc-notifications__toast--error {
            background: #3d1515;
            color: #f5c8c8;
            border-color: rgba(220, 80, 80, 0.45);
        }
        .dc-notifications__toast--info {
            background: #1a2740;
            color: #c8daf5;
            border-color: rgba(80, 140, 220, 0.4);
        }
        .dc-notifications__toast--default {
            background: #25252c;
            color: #e8e8ee;
        }
    `;
    document.head.appendChild(style);
}

function ensureContainer() {
    let el = document.getElementById(CONTAINER_ID);
    if (!el) {
        el = document.createElement('div');
        el.id = CONTAINER_ID;
        el.className = 'dc-notifications';
        el.setAttribute('aria-live', 'polite');
        el.setAttribute('aria-relevant', 'additions text');
        document.body.appendChild(el);
    }
    return el;
}

function normalizeLevel(level) {
    const key = String(level ?? 'info').toLowerCase();
    if (['success', 'warning', 'error', 'info', 'default'].includes(key)) {
        return key;
    }
    return 'info';
}

/**
 * Clefplay-style notifications for plain DOM (no Vue / Nuxt).
 *
 * @returns {{
 *   showQuick: (level: string, message: string, options?: { duration?: number }) => void,
 *   show: (config: string | { type?: string, message?: string, duration?: number }) => void,
 * }}
 */
export function useNotifications() {
    function showQuick(level, message, options = {}) {
        const text = message == null ? '' : String(message);
        const duration = typeof options.duration === 'number' ? options.duration : 4200;
        const variant = normalizeLevel(level);

        ensureNotificationStyles();
        const container = ensureContainer();

        const toast = document.createElement('div');
        toast.className = `dc-notifications__toast dc-notifications__toast--${variant}`;
        toast.setAttribute('role', 'alert');
        toast.tabIndex = 0;
        toast.title = 'Dismiss';

        const body = document.createElement('div');
        body.className = 'dc-notifications__body';
        body.textContent = text;
        toast.appendChild(body);

        container.appendChild(toast);

        requestAnimationFrame(() => {
            toast.classList.add('dc-notifications__toast--visible');
        });

        let timeoutId;
        const dismiss = () => {
            if (timeoutId) {
                clearTimeout(timeoutId);
                timeoutId = undefined;
            }
            toast.classList.remove('dc-notifications__toast--visible');
            setTimeout(() => toast.remove(), 220);
        };

        timeoutId = setTimeout(dismiss, duration);
        toast.addEventListener('click', dismiss);
    }

    function show(config) {
        if (typeof config === 'string') {
            showQuick('info', config);
            return;
        }
        if (!config || typeof config !== 'object') {
            return;
        }
        showQuick(config.type ?? 'info', config.message ?? '', {
            duration: config.duration,
        });
    }

    return {
        showQuick,
        show,
    };
}
