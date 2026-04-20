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
        <div class="ag-floating-filter-input GridTextCellEditor-root">
            <input type="text" class="ag-input-field-input ag-text-field-input" />
        </div>
    `.trim();
    return template.content.firstElementChild.cloneNode(true);
}

function getPatternRegex(pattern) {
    if (!pattern) {
        return null;
    }
    if (pattern instanceof RegExp) {
        return pattern;
    }
    try {
        return new RegExp(pattern);
    } catch (_error) {
        return null;
    }
}

export class GridTextCellEditor {
    init(params) {
        this.params = params;
        this.editorParams = { ...getCellEditorParams(params), ...(params ?? {}) };
        this.regex = getPatternRegex(this.editorParams.pattern);
        this.highlightAllOnFocus = true;

        this.eGui = createEditorTemplate();
        this.input = this.eGui.querySelector('input');

        this.setInitialState(params);
        this.value = this.input.value;
        this.lastValue = this.value;

        this.input.addEventListener('input', () => {
            const oldValue = this.lastValue;
            const newValue = this.input.value;

            if (this.isValueAllowed(newValue)) {
                this.value = newValue;
                this.lastValue = newValue;
                this.runOnUpdateCallback(oldValue, newValue);
                return;
            }

            this.input.value = oldValue;
            this.value = oldValue;
        });
    }

    setInitialState(params) {
        let startValue = '';
        this.highlightAllOnFocus = true;

        if (!params) {
            startValue = '';
        } else if (params.key === 'Backspace' || params.key === 'Delete') {
            startValue = '';
        } else if (params.charPress) {
            startValue = params.charPress;
            this.highlightAllOnFocus = false;
        } else {
            startValue = params.value == null ? '' : String(params.value);
            if (params.key === 'F2') {
                this.highlightAllOnFocus = false;
            }
        }

        this.input.value = startValue;
    }

    isValueAllowed(nextValue) {
        if (nextValue === '') {
            return true;
        }
        if (!this.regex) {
            return true;
        }
        this.regex.lastIndex = 0;
        return this.regex.test(nextValue);
    }

    runOnUpdateCallback(oldValue, newValue) {
        if (typeof this.editorParams.onUpdate !== 'function') {
            return;
        }

        const processedValue = this.editorParams.onUpdate(oldValue, newValue);
        if (processedValue == null || processedValue === newValue) {
            return;
        }

        if (String(processedValue).length === 11) {
            this.params?.api?.dispatchEvent({
                type: 'edit-select',
                data: {
                    value: processedValue,
                    column: this.params?.colDef,
                    rowNode: this.params?.node,
                },
            });
        }

        this.input.value = String(processedValue);
        this.value = this.input.value;
        this.lastValue = this.input.value;
    }

    getGui() {
        return this.eGui;
    }

    afterGuiAttached() {
        this.input?.focus();
        if (this.highlightAllOnFocus) {
            this.input?.select();
        }
    }

    focusIn() {
        this.input?.focus();
    }

    getValue() {
        const raw = this.input?.value ?? '';
        if (raw === '' && this.editorParams.emptyStringIsNull === true) {
            return null;
        }
        return typeof this.params?.value === 'number' ? Number(raw) : raw;
    }

    setValue(value) {
        this.input.value = value == null ? '' : String(value);
        this.value = this.input.value;
        this.lastValue = this.input.value;
    }

    isCancelAfterEnd() {
        return false;
    }

    isPopup() {
        return false;
    }
}
