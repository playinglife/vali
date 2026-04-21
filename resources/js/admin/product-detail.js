import { createAgGridCommon } from '../AgGridCommon.js';
import { buildProductDetailDefinitions } from './definitions/productDetailDefinitions.js';

(function () {
    function bindButtons(common) {
        const newButton = document.querySelector('[data-admin-grid-action="new"]');
        if (!newButton || typeof common?.addRow !== 'function') {
            return;
        }
        if (newButton.dataset.boundAddRow === '1') {
            return;
        }
        newButton.dataset.boundAddRow = '1';
        newButton.addEventListener('click', () => {
            common.addRow();
        });
    }

    function mapVariantOptionValues(variant, options) {
        const mapped = {};
        const allOptions = Array.isArray(options) ? options : [];
        const selectedOptions = Array.isArray(variant?.options) ? variant.options : [];

        allOptions.forEach((option) => {
            mapped[`option_${option?.id}_value_id`] = null;
        });

        selectedOptions.forEach((option) => {
            const values = Array.isArray(option?.values) ? option.values : [];
            const selected = values[0];
            mapped[`option_${option?.id}_value_id`] = selected?.id != null ? String(selected.id) : null;
        });

        return mapped;
    }

    function collectValueIdsFromOptionColumns(item, options) {
        const allOptions = Array.isArray(options) ? options : [];

        return allOptions
            .map((option) => item?.[`option_${option?.id}_value_id`])
            .filter((valueId) => valueId !== null && valueId !== undefined && valueId !== '')
            .map((valueId) => Number(valueId))
            .filter((valueId) => Number.isInteger(valueId) && valueId > 0);
    }

    function mapVariantsForGrid(variants, options) {
        const rows = Array.isArray(variants) ? variants : [];

        return rows.map((variant) => ({
            ...variant,
            ...mapVariantOptionValues(variant, options),
            value_ids: collectValueIdsFromOptionColumns(
                { ...variant, ...mapVariantOptionValues(variant, options) },
                options
            ),
        }));
    }

    function initGrid() {
        const gridEl = document.getElementById('admin-product-detail-grid');
        if (!gridEl || !window.agGrid) {
            return;
        }

        const options = (window.transferData && window.transferData.options) || [];
        const variants = (window.transferData && window.transferData.variants) || [];

        const { common } = createAgGridCommon({
            gridElement: gridEl,
            rowData: mapVariantsForGrid(variants, options),
            columnDefinitions: buildProductDetailDefinitions(options),
            gridOptions: {
                gridId: 'product-detail-grid',
                pagination: false,
                headerHeight: 40,
                rowDragManaged: false,
                suppressMoveWhenRowDragging: false,
            },
            gridCustom: {
                config: {
                    url: '/products/{id}',
                },
                onGridReady: (params) => {
                    const api = params?.api;
                    if (!api) {
                        return;
                    }
                    // Delay one frame so rendered cell content is measured correctly.
                    requestAnimationFrame(() => {
                        api.autoSizeAllColumns(false);
                    });
                },
                onCellValueChanged: (params) => {
                    if (!params?.data) {
                        return;
                    }
                    params.data.value_ids = collectValueIdsFromOptionColumns(params.data, options);
                },
                onRowEditingStopped: (params) => {
                    if (!params?.data) {
                        return;
                    }
                    params.data.value_ids = collectValueIdsFromOptionColumns(params.data, options);
                },
                prepareRecord: (item) => {
                    return {
                        id: item.id || '',
                        sku: item.sku || '',
                        price: item.price || '',
                        ...item,
                        value_ids: collectValueIdsFromOptionColumns(item, options),
                    };
                },
            },
        });
        bindButtons(common);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initGrid);
    } else {
        initGrid();
    }
})();
