function escapeAttr(value) {
    return String(value).replace(/"/g, '\\"');
}

function isCellContentOverflowing(params) {
    if (!params?.node || !params?.column) {
        return false;
    }

    const rowIndex = params.node.rowIndex;
    const colId = params.column.getColId ? params.column.getColId() : params.column.colId;
    if (rowIndex == null || !colId) {
        return false;
    }

    const cellSelector = `.ag-row[row-index="${escapeAttr(rowIndex)}"] .ag-cell[col-id="${escapeAttr(colId)}"]`;
    const cell = document.querySelector(cellSelector);
    if (!cell) {
        return false;
    }
    const content = cell.firstElementChild?.firstElementChild || cell.firstElementChild || cell;
    console.log(content);
    console.log(content.scrollWidth > content.clientWidth || content.scrollHeight > content.clientHeight);
    return content.scrollWidth > content.clientWidth || content.scrollHeight > content.clientHeight;
}

export class NewGridTooltip {
    init(params) {
        this.params = params;
        this.eGui = document.createElement('div');
        this.eGui.className = 'NewGridTooltip-root';

        const shouldShow = params?.show !== 'overflow' || isCellContentOverflowing(params);
        if (shouldShow) {
            this.eGui.textContent = params?.value ?? '';
        }
    }

    getGui() {
        return this.eGui;
    }
}