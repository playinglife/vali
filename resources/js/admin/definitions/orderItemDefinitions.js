import { GridSelectCellEditor } from '../../ag-grid/cellEditors/GridSelectCellEditor.js';
import { GridSelectCellRenderer } from '../../ag-grid/cellRenderers/GridSelectCellRenderer.js';
import { GridTextCellEditor } from '../../ag-grid/cellEditors/GridTextCellEditor.js';

const decimalParams = {
    pattern: '^\\d*(?:\\.\\d{0,2})?$',
    emptyStringIsNull: true,
};

const discountTypeList = [
    { id: 'percentage', name: 'Percentage' },
    { id: 'fixed', name: 'Fixed' },
];

export const orderItemColumnDefs = [
    {
        field: 'id',
        headerName: 'ID',
        editable: false,
        maxWidth: 90,
        sortable: true,
        filter: 'agNumberColumnFilter',
    },
    {
        field: 'product_id',
        headerName: 'Product ID',
        editable: true,
        sortable: true,
        filter: 'agNumberColumnFilter',
        maxWidth: 120,
        cellEditor: GridTextCellEditor,
        cellEditorParams: {
            pattern: '^\\d*$',
            emptyStringIsNull: true,
        },
    },
    {
        field: 'variant_id',
        headerName: 'Variant ID',
        editable: true,
        sortable: true,
        filter: 'agNumberColumnFilter',
        maxWidth: 120,
        cellEditor: GridTextCellEditor,
        cellEditorParams: {
            pattern: '^\\d*$',
            emptyStringIsNull: true,
        },
    },
    {
        field: 'product_name',
        headerName: 'Product',
        editable: false,
        sortable: true,
        filter: true,
        minWidth: 140,
    },
    {
        field: 'variant_sku',
        headerName: 'Variant SKU',
        editable: false,
        sortable: true,
        filter: true,
        minWidth: 120,
    },
    {
        field: 'sku',
        headerName: 'SKU',
        editable: true,
        sortable: true,
        filter: true,
        minWidth: 120,
    },
    {
        field: 'quantity',
        headerName: 'Qty',
        editable: true,
        sortable: true,
        filter: 'agNumberColumnFilter',
        maxWidth: 100,
        cellEditor: GridTextCellEditor,
        cellEditorParams: {
            pattern: '^\\d{1,6}$',
            emptyStringIsNull: true,
        },
    },
    {
        field: 'price',
        headerName: 'Price',
        editable: true,
        sortable: true,
        filter: 'agNumberColumnFilter',
        cellEditor: GridTextCellEditor,
        cellEditorParams: decimalParams,
    },
    {
        field: 'discount_type',
        headerName: 'Discount type',
        editable: true,
        sortable: true,
        filter: true,
        cellRenderer: GridSelectCellRenderer,
        cellRendererParams: {
            valueList: discountTypeList,
        },
        cellEditor: GridSelectCellEditor,
        cellEditorParams: {
            valueList: discountTypeList,
        },
    },
    {
        field: 'discount',
        headerName: 'Discount',
        editable: true,
        sortable: true,
        filter: 'agNumberColumnFilter',
        cellEditor: GridTextCellEditor,
        cellEditorParams: decimalParams,
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
        field: 'created_at',
        headerName: 'Created',
        editable: false,
        sortable: true,
        filter: true,
        minWidth: 160,
    },
    {
        field: 'updated_at',
        headerName: 'Updated',
        editable: false,
        sortable: true,
        filter: true,
        minWidth: 160,
    },
];
