const STYLE_MARKER = 'data-dc-dialog-styles';

function ensureDialogStyles() {
    if (document.head.querySelector(`[${STYLE_MARKER}]`)) {
        return;
    }
    const style = document.createElement('style');
    style.setAttribute(STYLE_MARKER, '1');
    style.textContent = `
        .dc-dialog-host {
            position: fixed;
            inset: 0;
            z-index: 100000;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
            box-sizing: border-box;
        }
        .dc-dialog-host[hidden] {
            display: none !important;
        }
        .dc-dialog__backdrop {
            position: absolute;
            inset: 0;
            background: rgba(0, 0, 0, 0.45);
        }
        .dc-dialog__panel {
            position: relative;
            z-index: 1;
            min-width: min(100%, 22rem);
            max-width: min(100%, 32rem);
            max-height: min(90vh, 28rem);
            display: flex;
            flex-direction: column;
            background: #1a1a1f;
            color: #f2f2f5;
            border-radius: 10px;
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.45);
            border: 1px solid rgba(255, 255, 255, 0.08);
            overflow: hidden;
        }
        .dc-dialog__title {
            padding: 0.85rem 1rem 0.35rem;
            font-size: 0.95rem;
            font-weight: 600;
        }
        .dc-dialog__title:empty {
            display: none;
        }
        .dc-dialog__body {
            padding: 0.5rem 1rem 1rem;
            font-size: 0.875rem;
            line-height: 1.45;
            overflow: auto;
            flex: 1;
        }
        .dc-dialog__footer {
            display: flex;
            justify-content: flex-end;
            gap: 0.5rem;
            padding: 0.65rem 1rem 0.85rem;
            border-top: 1px solid rgba(255, 255, 255, 0.08);
            flex-wrap: wrap;
        }
        .dc-dialog__btn {
            appearance: none;
            border-radius: 6px;
            padding: 0.45rem 0.9rem;
            font-size: 0.8125rem;
            font-weight: 600;
            cursor: pointer;
            border: 1px solid transparent;
        }
        .dc-dialog__btn:focus-visible {
            outline: 2px solid rgba(100, 160, 255, 0.85);
            outline-offset: 2px;
        }
        .dc-dialog__btn--primary {
            background: #3b6cff;
            color: #fff;
        }
        .dc-dialog__btn--primary:hover {
            background: #2f58d6;
        }
        .dc-dialog__btn--terniary {
            background: transparent;
            color: #c8cad4;
            border-color: rgba(255, 255, 255, 0.2);
        }
        .dc-dialog__btn--terniary:hover {
            background: rgba(255, 255, 255, 0.06);
        }
        .dc-dialog__btn--default {
            background: rgba(255, 255, 255, 0.1);
            color: #f2f2f5;
        }
    `;
    document.head.appendChild(style);
}

function readShow(show) {
    if (show == null) {
        return true;
    }
    if (typeof show === 'object' && show !== null && 'value' in show) {
        return !!show.value;
    }
    return !!show;
}

/**
 * Clefplay-style dynamic dialog: mounts to document.body, respects ref-like `show`
 * (caller often sets `show.value = true` right after createComponent returns).
 *
 * @param {object} props
 * @param {string} [props.title]
 * @param {string} [props.content]
 * @param {{ value: boolean }|boolean} [props.show]
 * @param {Array<{ label: string, color?: string, action?: () => void }>} [props.buttons]
 * @returns {{ destroy: () => void }}
 */
export function createDialogComponent(props) {
    ensureDialogStyles();

    const titleText = props.title ?? '';
    const content = props.content ?? '';
    const show = props.show;
    const buttons = Array.isArray(props.buttons) ? props.buttons : [];

    const host = document.createElement('div');
    host.className = 'dc-dialog-host';
    host.setAttribute('role', 'dialog');
    host.setAttribute('aria-modal', 'true');
    host.hidden = true;

    const backdrop = document.createElement('div');
    backdrop.className = 'dc-dialog__backdrop';
    backdrop.tabIndex = -1;

    const panel = document.createElement('div');
    panel.className = 'dc-dialog__panel';

    const titleEl = document.createElement('div');
    titleEl.className = 'dc-dialog__title';
    if (titleText) {
        titleEl.textContent = titleText;
    }

    const body = document.createElement('div');
    body.className = 'dc-dialog__body';
    if (typeof content === 'string') {
        body.textContent = content;
    } else if (content && typeof content === 'object' && content.nodeType === 1) {
        body.appendChild(content);
    }

    const footer = document.createElement('div');
    footer.className = 'dc-dialog__footer';

    const buttonElements = [];
    for (const btn of buttons) {
        const el = document.createElement('button');
        el.type = 'button';
        el.className = 'dc-dialog__btn';
        const color = (btn.color || 'default').toLowerCase();
        if (color === 'primary') {
            el.classList.add('dc-dialog__btn--primary');
        } else if (color === 'terniary' || color === 'tertiary') {
            el.classList.add('dc-dialog__btn--terniary');
        } else {
            el.classList.add('dc-dialog__btn--default');
        }
        el.textContent = btn.label ?? '';
        el.addEventListener('click', (e) => {
            e.preventDefault();
            btn.action?.();
        });
        footer.appendChild(el);
        buttonElements.push(el);
    }

    panel.appendChild(titleEl);
    panel.appendChild(body);
    panel.appendChild(footer);
    host.appendChild(backdrop);
    host.appendChild(panel);
    document.body.appendChild(host);

    const applyVisibility = (visible) => {
        host.hidden = !visible;
        host.setAttribute('aria-hidden', visible ? 'false' : 'true');
        if (visible) {
            const focusTarget = buttonElements[0] || panel;
            queueMicrotask(() => focusTarget.focus?.());
        }
    };

    const syncFromShow = () => applyVisibility(readShow(show));

    queueMicrotask(syncFromShow);

    const onKeyDown = (e) => {
        if (e.key === 'Escape' && !host.hidden) {
            e.preventDefault();
            const first = buttons[0];
            if (first?.action) {
                first.action();
            } else {
                destroy();
            }
        }
    };
    document.addEventListener('keydown', onKeyDown);

    function destroy() {
        document.removeEventListener('keydown', onKeyDown);
        host.remove();
    }

    return { destroy };
}
