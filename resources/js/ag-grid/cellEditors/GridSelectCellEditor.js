function getCellEditorParams(params) {
    if (!params?.colDef?.cellEditorParams) {
        return {};
    }
    return typeof params.colDef.cellEditorParams === 'function'
        ? params.colDef.cellEditorParams(params) || {}
        : params.colDef.cellEditorParams;
}

function createEditorTemplate() {
    const template = document.createElement('template');
    template.innerHTML = `
        <div class="neo-inputer GridSelectCellEditor-root" style="width: 100%;">
            <select class="GridSelectCellEditor-select" style="width: 100%; height: 100%; border: none; outline: none; background: transparent; font-size: 14px;"></select>
        </div>
    `.trim();
    return template.content.firstElementChild.cloneNode(true);
}

export class GridSelectCellEditor {
    init(params) {
        this.params = params;
        this.value = params.value ?? null;

        const editorParams = getCellEditorParams(params);
        const valueList = Array.isArray(editorParams.valueList) ? editorParams.valueList : [];

        this.eGui = createEditorTemplate();
        this.select = this.eGui.querySelector('.GridSelectCellEditor-select');

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
