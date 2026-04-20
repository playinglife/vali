import { createAgGridCommon } from '../AgGridCommon.js';
import { productColumnDefs } from './definitions/productDefinitions.js';

(function () {
    function initGrid() {
        const gridEl = document.getElementById('admin-products-grid');
        if (!gridEl || !window.agGrid) {
            return;
        }

        createAgGridCommon({
            gridElement: gridEl,
            rowData: (window.transferData && window.transferData.products) || [],
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
                    url: '/products',
                },
                prepareRecord: (item) => {
                    return {
                        id: item.id || '',
                        name: (item.name?.split('.'))?.at(-1) || '',
                        parent_id: item.parent_id ?? null,
                        description: item.description || '',
                        created_at: item.created_at || '',
                        updated_at: item.updated_at || '',
                    };
                },
            },
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initGrid);
    } else {
        initGrid();
    }
})();
