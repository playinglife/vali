function getCellEditorParams(params) {
    if (!params?.colDef?.cellEditorParams) {
        return {};
    }
    return typeof params.colDef.cellEditorParams === 'function'
        ? params.colDef.cellEditorParams(params) || {}
        : params.colDef.cellEditorParams;
}

export class GridSelectCellEditor {
    init(params) {
        this.params = params;
        this.value = params.value ?? null;

        const editorParams = getCellEditorParams(params);
        const valueList = Array.isArray(editorParams.valueList) ? editorParams.valueList : [];

        this.eGui = document.createElement('div');
        this.eGui.className = 'neo-inputer GridSelectCellEditor-root';

        this.select = document.createElement('select');
        this.select.style.width = '100%';
        this.select.style.height = '100%';
        this.select.style.border = 'none';
        this.select.style.outline = 'none';
        this.select.style.background = 'transparent';
        this.select.style.fontSize = '14px';

        // Optional empty choice for nullable values.
        const emptyOption = document.createElement('option');
        emptyOption.value = '';
        emptyOption.textContent = '';
        this.select.appendChild(emptyOption);

        valueList.forEach((item) => {
            if (item?.noSelect) {
                return;
            }
            const option = document.createElement('option');
            option.value = String(item.id ?? '');
            option.textContent = String(item.name ?? item.id ?? '');
            this.select.appendChild(option);
        });

        const current = this.value == null ? '' : String(this.value);
        this.select.value = current;

        this.select.addEventListener('change', () => {
            const raw = this.select.value;
            this.value = raw === '' ? null : raw;
        });

        this.eGui.appendChild(this.select);
    }

    getGui() {
        return this.eGui;
    }

    afterGuiAttached() {
        this.select?.focus();
        this.select?.click();
    }

    getValue() {
        return this.value;
    }

    isPopup() {
        return false;
    }
}
