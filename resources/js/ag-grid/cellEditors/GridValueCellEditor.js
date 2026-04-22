function getCellEditorParams(params) {
    if (!params?.colDef?.cellEditorParams) {
        return {};
    }
    return typeof params.colDef.cellEditorParams === 'function'
        ? params.colDef.cellEditorParams(params) || {}
        : params.colDef.cellEditorParams;
}

function createEditorRoot() {
    const root = document.createElement('div');
    root.style.display = 'flex';
    root.style.flexWrap = 'wrap';
    root.style.gap = '0.5rem';
    root.style.padding = '0.1rem 1em';
    return root;
}

export class GridValueCellEditor {
    init(params) {
        this.params = params;
        this.editorParams = getCellEditorParams(params);
        this.value = params.value == null ? null : String(params.value);
        this.values = Array.isArray(this.editorParams.values) ? this.editorParams.values : [];
        this.optionId = this.editorParams.optionId ?? 'option';
        this.eGui = createEditorRoot();
        this.firstInput = null;

        this.values.forEach((value) => {
            const valueId = String(value.id ?? '');
            const valueLabel = String(value.value ?? valueId);

            const label = document.createElement('label');
            label.style.display = 'inline-flex';
            label.style.alignItems = 'center';
            label.style.gap = '0.25rem';
            label.style.cursor = 'pointer';
            label.style.fontSize = '0.8rem';

            const input = document.createElement('input');
            input.type = 'radio';
            input.name = `editor-option-${this.optionId}-${params.node?.id ?? ''}`;
            input.value = valueId;
            input.checked = valueId === this.value;
            input.addEventListener('change', () => {
                if (input.checked) {
                    this.value = valueId;
                }
            });

            if (!this.firstInput) {
                this.firstInput = input;
            }

            const text = document.createElement('span');
            text.textContent = valueLabel;

            label.appendChild(input);
            label.appendChild(text);
            this.eGui.appendChild(label);
        });
    }

    getGui() {
        return this.eGui;
    }

    afterGuiAttached() {
        this.firstInput?.focus();
    }

    getValue() {
        return this.value;
    }

    isPopup() {
        return false;
    }
}
