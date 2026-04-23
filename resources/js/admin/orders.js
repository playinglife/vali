import { createAgGridCommon } from '../AgGridCommon.js';
import { orderColumnDefs } from './definitions/orderDefinitions.js';

(function () {
    function bindButtons(grid, common) {
        const deleteButton = document.querySelector('[data-admin-grid-action="delete"]');
        deleteButton.addEventListener('click', () => {
            common.deleteRows(grid.gridApi.getSelectedNodes().map(node => node.data.id));
        });
        const refreshButton = document.querySelector('[data-admin-grid-action="refresh"]');
        refreshButton.addEventListener('click', () => {
            common.reloadGrid();
        });
    }

    function goToOrderDetail(order) {
        if (!order || !order.id) {
            return;
        }
        const template = window.transferData?.orderDetailUrlTemplate || '';
        if (!template) {
            return;
        }
        window.location.href = template.replace('__ORDER_ID__', String(order.id));
    }

    function initGrid() {
        const gridEl = document.getElementById('admin-orders-grid');
        if (!gridEl || !window.agGrid) {
            return;
        }

        const { grid, common } = createAgGridCommon({
            gridElement: gridEl,
            //rowData: (window.transferData && window.transferData.products) || [],
            rowData: (window.transferData && window.transferData.orders) || [],
            columnDefinitions: orderColumnDefs,
            gridOptions: {
                gridId: 'orders',
                pagination: false,
                headerHeight: 40,
                rowDragManaged: false,
                suppressMoveWhenRowDragging: false,
            },
            gridCustom: {
                config: {
                    url: '/admin/order-records',
                },
                prepareRecord: (item) => {
                    return {
                        id: item.id || '',
                        user_id: item.user_id || '',
                        order_number: item.order_number || '',
                        email: item.email || '',
                        status: item.status || '',
                        currency: item.currency || '',
                        shipping_total: parseFloat(item.shipping_total ?? '') || 0,
                        billing_address: item.billing_address || '',
                        shipping_address: item.shipping_address || '',
                        notes: item.notes || '',
                        placed_at: item.placed_at || '',
                        paid_at: item.paid_at || '',
                        created_at: item.created_at || '',
                        updated_at: item.updated_at || '',
                    };
                },
                onRowDoubleClicked: (params) => {
                    goToOrderDetail(params?.data);
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
