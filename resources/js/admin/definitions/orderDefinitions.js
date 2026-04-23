import { GridTextCellEditor } from '../../ag-grid/cellEditors/GridTextCellEditor.js';

const decimalParams = {
    pattern: '^\\d*(?:\\.\\d{0,2})?$',
    emptyStringIsNull: true,
};

function formatJsonCell(params) {
    const v = params.value;
    if (v == null || v === '') {
        return '';
    }
    if (typeof v === 'string') {
        return v;
    }
    try {
        return JSON.stringify(v);
    } catch {
        return String(v);
    }
}

export const orderColumnDefs = [
    {
        field: 'id',
        headerName: 'ID',
        editable: false,
        maxWidth: 90,
        sortable: true,
        filter: 'agNumberColumnFilter',
    },
    {
        field: 'user_id',
        headerName: 'User ID',
        editable: true,
        sortable: true,
        filter: 'agNumberColumnFilter',
        maxWidth: 110,
        cellEditor: GridTextCellEditor,
        cellEditorParams: {
            pattern: '^\\d*$',
            emptyStringIsNull: true,
        },
    },
    {
        field: 'order_number',
        headerName: 'Order number',
        editable: true,
        sortable: true,
        filter: true,
        minWidth: 140,
    },
    {
        field: 'email',
        headerName: 'Email',
        editable: true,
        sortable: true,
        filter: true,
        flex: 1,
        minWidth: 200,
    },
    {
        field: 'status',
        headerName: 'Status',
        editable: true,
        sortable: true,
        filter: true,
        minWidth: 120,
    },
    {
        field: 'currency',
        headerName: 'Currency',
        editable: true,
        sortable: true,
        filter: true,
        maxWidth: 100,
    },
    {
        field: 'shipping_total',
        headerName: 'Shipping total',
        editable: true,
        sortable: true,
        filter: 'agNumberColumnFilter',
        cellEditor: GridTextCellEditor,
        cellEditorParams: decimalParams,
    },
    {
        field: 'billing_address',
        headerName: 'Billing address',
        editable: false,
        sortable: false,
        filter: true,
        flex: 1,
        minWidth: 180,
        valueFormatter: formatJsonCell,
    },
    {
        field: 'shipping_address',
        headerName: 'Shipping address',
        editable: false,
        sortable: false,
        filter: true,
        flex: 1,
        minWidth: 180,
        valueFormatter: formatJsonCell,
    },
    {
        field: 'notes',
        headerName: 'Notes',
        editable: true,
        sortable: true,
        filter: true,
        flex: 1,
        minWidth: 160,
    },
    {
        field: 'placed_at',
        headerName: 'Placed at',
        editable: true,
        sortable: true,
        filter: true,
        minWidth: 170,
    },
    {
        field: 'paid_at',
        headerName: 'Paid at',
        editable: true,
        sortable: true,
        filter: true,
        minWidth: 170,
    },
    {
        field: 'created_at',
        headerName: 'Created',
        editable: false,
        sortable: true,
        filter: true,
        minWidth: 170,
    },
    {
        field: 'updated_at',
        headerName: 'Updated',
        editable: false,
        sortable: true,
        filter: true,
        minWidth: 170,
    },
];
