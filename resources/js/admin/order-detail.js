import { createAgGridCommon } from '../AgGridCommon.js';
import { orderItemColumnDefs } from './definitions/orderItemDefinitions.js';

(function () {
    function bindButtons(grid, common) {
        const newButton = document.querySelector('[data-admin-grid-action="new"]');
        newButton.addEventListener('click', () => {
            common.addRow();
        });
        const deleteButton = document.querySelector('[data-admin-grid-action="delete"]');
        deleteButton.addEventListener('click', () => {
            common.deleteRows(grid.gridApi.getSelectedNodes().map((node) => node.data.id));
        });
        const duplicateButton = document.querySelector('[data-admin-grid-action="duplicate"]');
        duplicateButton.addEventListener('click', () => {
            const selectedRow = grid.gridApi.getSelectedNodes()[0];
            if (!selectedRow) {
                return;
            }
            const newData = { ...selectedRow.data };
            delete newData.id;
            delete newData.created_at;
            delete newData.updated_at;
            const newRowUniqueId = common.addRow();
            common.setCurrentEditedRowValues(newRowUniqueId, newData);
        });
        const refreshButton = document.querySelector('[data-admin-grid-action="refresh"]');
        refreshButton.addEventListener('click', () => {
            common.reloadGrid();
        });
    }

    function initGrid() {
        const gridEl = document.getElementById('admin-order-detail-grid');
        if (!gridEl || !window.agGrid) {
            return;
        }

        const orderId = window.transferData?.order?.id;
        if (!orderId) {
            return;
        }

        const itemsUrl = `/admin/orders/${String(orderId)}/items`;

        const { grid, common } = createAgGridCommon({
            gridElement: gridEl,
            rowData: [],
            columnDefinitions: orderItemColumnDefs,
            gridOptions: {
                gridId: 'order-detail-items',
                pagination: false,
                headerHeight: 40,
                rowDragManaged: false,
                suppressMoveWhenRowDragging: false,
            },
            gridCustom: {
                config: {
                    url: itemsUrl,
                },
                prepareRecord: (item) => {
                    return {
                        id: item.id || '',
                        order_id: item.order_id ?? orderId,
                        product_id: item.product_id ?? '',
                        variant_id: item.variant_id ?? '',
                        product_name: item.product?.name ?? '',
                        variant_sku: item.variant?.sku ?? '',
                        sku: item.sku || '',
                        quantity: item.quantity ?? 1,
                        price: parseFloat(item.price ?? '') || 0,
                        discount_type: item.discount_type || '',
                        discount: item.discount != null ? parseFloat(item.discount) : null,
                        currency: item.currency || 'RON',
                        created_at: item.created_at || '',
                        updated_at: item.updated_at || '',
                    };
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
