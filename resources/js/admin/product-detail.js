import { createAgGridCommon } from '../AgGridCommon.js';
import { buildProductDetailDefinitions } from './definitions/productDetailDefinitions.js';

(function () {
    let grid = null
    let common = null
    function bindButtons(grid, common) {
        const newButton = document.querySelector('[data-admin-grid-action="new"]');
        newButton.addEventListener('click', () => {
            common.addRow();
        });
        const deleteButton = document.querySelector('[data-admin-grid-action="delete"]');
        deleteButton.addEventListener('click', () => {
            common.deleteRows(grid.gridApi.getSelectedNodes().map(node => node.data.id));
        });
        const duplicateButton = document.querySelector('[data-admin-grid-action="duplicate"]');
        duplicateButton.addEventListener('click', () => {
            common.duplicateRow();
        });
        const refreshButton = document.querySelector('[data-admin-grid-action="refresh"]');
        refreshButton.addEventListener('click', () => {
            common.reloadGrid();
        });
        const variateButton = document.querySelector('[data-admin-grid-action="variate"]');
        variateButton.addEventListener('click', () => {
            createAllPossibleVariations();
        });
    }

    function selectOptionsForVariationCreation(options) {
        return new Promise((resolve) => {
            const availableOptions = Array.isArray(options) ? options : [];
            const overlay = document.createElement('div');
            overlay.style.position = 'fixed';
            overlay.style.inset = '0';
            overlay.style.background = 'rgba(0, 0, 0, 0.35)';
            overlay.style.display = 'flex';
            overlay.style.alignItems = 'center';
            overlay.style.justifyContent = 'center';
            overlay.style.zIndex = '100002';

            const dialog = document.createElement('div');
            dialog.style.background = '#fff';
            dialog.style.width = 'min(92vw, 460px)';
            dialog.style.maxHeight = '80vh';
            dialog.style.overflow = 'auto';
            dialog.style.borderRadius = '8px';
            dialog.style.padding = '1rem';
            dialog.style.boxShadow = '0 12px 40px rgba(0, 0, 0, 0.3)';

            const title = document.createElement('h3');
            title.textContent = 'Select options for variation creation';
            title.style.margin = '0 0 0.75rem 0';
            title.style.fontSize = '1rem';

            const list = document.createElement('div');
            list.style.display = 'flex';
            list.style.flexDirection = 'column';
            list.style.gap = '0.45rem';
            list.style.marginBottom = '1rem';

            const checkboxRefs = [];
            availableOptions.forEach((option) => {
                const optionId = Number(option?.id);
                if (!Number.isInteger(optionId) || optionId <= 0) {
                    return;
                }
                const wrapper = document.createElement('label');
                wrapper.style.display = 'flex';
                wrapper.style.alignItems = 'center';
                wrapper.style.gap = '0.5rem';
                wrapper.style.cursor = 'pointer';

                const checkbox = document.createElement('input');
                checkbox.type = 'checkbox';
                checkbox.checked = true;
                checkbox.value = String(optionId);

                const text = document.createElement('span');
                text.textContent = String(option?.name || `Option ${optionId}`);

                wrapper.appendChild(checkbox);
                wrapper.appendChild(text);
                list.appendChild(wrapper);
                checkboxRefs.push(checkbox);
            });

            const actions = document.createElement('div');
            actions.style.display = 'flex';
            actions.style.justifyContent = 'flex-end';
            actions.style.gap = '0.5rem';

            const cancelButton = document.createElement('button');
            cancelButton.type = 'button';
            cancelButton.textContent = 'Cancel';

            const createButton = document.createElement('button');
            createButton.type = 'button';
            createButton.textContent = 'Create';

            cancelButton.addEventListener('click', () => {
                overlay.remove();
                resolve(null);
            });
            createButton.addEventListener('click', () => {
                const selectedIds = checkboxRefs
                    .filter((input) => input.checked)
                    .map((input) => Number(input.value))
                    .filter((id) => Number.isInteger(id) && id > 0);
                overlay.remove();
                resolve(selectedIds);
            });
            overlay.addEventListener('click', (event) => {
                if (event.target === overlay) {
                    overlay.remove();
                    resolve(null);
                }
            });

            actions.appendChild(cancelButton);
            actions.appendChild(createButton);
            dialog.appendChild(title);
            dialog.appendChild(list);
            dialog.appendChild(actions);
            overlay.appendChild(dialog);
            document.body.appendChild(overlay);
        });
    }

    function mapVariantOptionValues(variant, options) {
        const mapped = {};
        const allOptions = Array.isArray(options) ? options : [];
        const selectedOptions = Array.isArray(variant?.options) ? variant.options : [];

        allOptions.forEach((option) => {
            const fieldName = option?.name;
            if (fieldName) {
                mapped[fieldName] = null;
            }
        });

        selectedOptions.forEach((option) => {
            const values = Array.isArray(option?.values) ? option.values : [];
            const selected = values[0];
            const fieldName = option?.name;
            if (fieldName) {
                mapped[fieldName] = selected?.id != null ? String(selected.id) : null;
            }
        });

        return mapped;
    }

    function collectValueIdsFromOptionColumns(item, options) {
        const allOptions = Array.isArray(options) ? options : [];

        return allOptions
            .map((option) => item?.[option?.name])
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

        const { grid:grid1, common: common1 } = createAgGridCommon({
            gridElement: gridEl,
            //rowData: mapVariantsForGrid(variants, options),
            rowData: [],
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
                    url: '/admin/products/' + String(window.transferData?.product?.id) + '/variants',
                    mainData: 'product.variants',
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
                    const optionValues = mapVariantOptionValues(item, options);
                    return {
                        id: item.id || '',
                        sku: item.sku || '',
                        price: item.price || '',
                        stock_quantity: item.stock_quantity ?? 0,
                        discount_type: item.discount_type || '',
                        discount: item.discount ?? 0,
                        is_active: Boolean(item.is_active),
                        image: item.image || '',
                        ...optionValues,
                        value_ids: collectValueIdsFromOptionColumns({ ...item, ...optionValues }, options),
                    };
                },
            },
        });
        grid = grid1
        common = common1
        bindButtons(grid1, common1);
    }

    async function createAllPossibleVariations() {
        const selectedOptionIds = await selectOptionsForVariationCreation(window.transferData?.options);
        if (!Array.isArray(selectedOptionIds)) {
            return;
        }
        if (selectedOptionIds.length === 0) {
            window.alert('Select at least one option.');
            return;
        }
        grid.gridApi.setGridOption('loading', true)
        axios.post('/admin/products/' + String(window.transferData?.product?.id) + '/variants/create-all', {
            option_ids: selectedOptionIds,
        }).then(function (response) {
            common.reloadGrid();
        }).catch(function (error) {
            window.alert(error.message);
        }).finally(() => {
            grid.gridApi.setGridOption('loading', false);
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initGrid);
    } else {
        initGrid();
    }
})();
