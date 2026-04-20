export class NoData {
    init(params = {}) {
        this.params = params;
        this.eGui = document.createElement('div');
        this.eGui.className = 'NoData-root';

        const label = document.createElement('span');
        label.textContent = 'No Records';
        this.eGui.appendChild(label);

        if (typeof params.click === 'function') {
            this.eGui.addEventListener('click', () => params.click(params));
        }
    }

    getGui() {
        return this.eGui;
    }

    destroy() {
        if (this.eGui) {
            this.eGui.replaceChildren();
        }
    }
}