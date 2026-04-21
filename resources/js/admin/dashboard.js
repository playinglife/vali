import { createAgGridCommon } from '../AgGridCommon.js';
import { productColumnDefs } from './definitions/productDefinitions.js';

(function () {
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
            const selectedRow = grid.gridApi.getSelectedNodes()[0];
            if (!selectedRow) {
                return;
            }
            const newData = {
                ...selectedRow.data,
                variants_count: 0,
            };
            delete newData.id;
            const newRowUniqueId = common.addRow();
            common.setCurrentEditedRowValues(newRowUniqueId, newData);
        });
        const refreshButton = document.querySelector('[data-admin-grid-action="refresh"]');
        refreshButton.addEventListener('click', () => {
            common.reloadGrid();
        });
    }

    function goToProductDetail(product) {
        if (!product || !product.id) {
            return;
        }
        const template = window.transferData?.productDetailUrlTemplate || '';
        if (!template) {
            return;
        }
        window.location.href = template.replace('__PRODUCT_ID__', String(product.id));
    }

    function initGrid() {
        const gridEl = document.getElementById('admin-products-grid');
        if (!gridEl || !window.agGrid) {
            return;
        }

        const { grid, common } = createAgGridCommon({
            gridElement: gridEl,
            //rowData: (window.transferData && window.transferData.products) || [],
            rowData: [],
            columnDefinitions: productColumnDefs,
            gridOptions: {
                gridId: 'products',
                pagination: false,
                headerHeight: 40,
                rowDragManaged: false,
                suppressMoveWhenRowDragging: false,
            },
            gridCustom: {
                config: {
                    url: '/admin/products',
                },
                prepareRecord: (item) => {
                    return {
                        id: item.id || '',
                        sku: item.sku || '',
                        name: item.name || '',
                        slug: item.slug || '',
                        price: parseFloat(item.price ?? '') || 0,
                        is_active: Boolean(item.is_active),
                        variants_count: item.variants_count ?? 0,
                        discount_type: item.discount_type || '',
                        discount: parseFloat(item.discount ?? '') || 0,
                        meta_title: item.meta_title || '',
                        meta_description: item.meta_description || '',
                        created_at: item.created_at || '',
                        updated_at: item.updated_at || '',
                        variants: Array.isArray(item.variants) ? item.variants : [],
                    };
                },
                onRowDoubleClicked: (params) => {
                    goToProductDetail(params?.data);
                },
            },
        });
        bindButtons(grid, common);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initGrid);
    } else {
        initGrid();
    }
})();
