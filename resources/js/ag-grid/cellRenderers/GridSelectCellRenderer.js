function resolveValueList(params) {
    if (Array.isArray(params?.valueList)) {
        return params.valueList;
    }
    const editorParams = params?.colDef?.cellEditorParams;
    const resolved = typeof editorParams === 'function' ? editorParams(params) : editorParams;
    return Array.isArray(resolved?.valueList) ? resolved.valueList : [];
}

export class GridSelectCellRenderer {
    init(params) {
        this.params = params;
        this.eGui = document.createElement('div');
        this.eGui.className = 'GridSelectCellRenderer-root';
        this.refresh(params);
    }

    getGui() {
        return this.eGui;
    }

    refresh(params) {
        this.params = params;
        this.eGui.replaceChildren();

        const list = resolveValueList(params);
        const item = list.find((entry) => String(entry?.id) === String(params?.value));
        if (!item) {
            return true;
        }

        const showImage = params?.showImage !== false;
        const showText = params?.showText !== false;

        if (showImage && item.icon) {
            const iconWrap = document.createElement('div');
            iconWrap.className = 'x-icon';
            // In plain JS mode we only support string-based icon content.
            if (typeof item.icon === 'string') {
                iconWrap.textContent = item.icon;
            }
            this.eGui.appendChild(iconWrap);
        }

        if (showText && item.name) {
            const textWrap = document.createElement('div');
            textWrap.className = 'x-text';
            if (showImage && item.icon) {
                textWrap.classList.add('x-text--with-icon');
            }
            const span = document.createElement('span');
            span.textContent = item.name;
            textWrap.appendChild(span);
            this.eGui.appendChild(textWrap);
        }

        return true;
    }
}
