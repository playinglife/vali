import { GridSelectCellEditor } from '../../ag-grid/cellEditors/GridSelectCellEditor.js';
import { GridSelectCellRenderer } from '../../ag-grid/cellRenderers/GridSelectCellRenderer.js';
import { GridTextCellEditor } from '../../ag-grid/cellEditors/GridTextCellEditor.js';

export const productColumnDefs = [
    {
        field: 'id',
        headerName: 'ID',
        editable: false,
        maxWidth: 90,
        sortable: true,
        filter: 'agNumberColumnFilter',
    },
    {
        field: 'sku',
        headerName: 'SKU',
        editable: true,
        sortable: true,
        filter: true,
    },
    {
        field: 'name',
        headerName: 'Name',
        editable: true,
        sortable: true,
        filter: true,
        flex: 1,
        minWidth: 220,
    },
    {
        field: 'slug',
        headerName: 'Slug',
        editable: true,
        sortable: true,
        filter: true,
        flex: 1,
        minWidth: 220,
    },
    {
        field: 'price',
        headerName: 'Price',
        editable: true,
        sortable: true,
        filter: 'agNumberColumnFilter',
        cellEditor: GridTextCellEditor,
        cellEditorParams: {
            pattern: '^\\d*(?:\\.\\d{0,2})?$',
            emptyStringIsNull: true,
        },
    },
    {
        field: 'discount_type',
        headerName: 'Discount Type',
        editable: true,
        sortable: true,
        filter: true,
        cellRenderer: GridSelectCellRenderer,
        cellRendererParams: {
          valueList: [
              { id: 'percentage', name: 'Percentage' },
              { id: 'fixed', name: 'Fixed' },
          ],
        },
        cellEditor: GridSelectCellEditor,
        cellEditorParams: {
          valueList: [
              { id: 'percentage', name: 'Percentage' },
              { id: 'fixed', name: 'Fixed' },
          ],
        },
    },
    {
        field: 'discount',
        headerName: 'Discount',
        editable: true,
        sortable: true,
        filter: 'agNumberColumnFilter',
        cellEditor: GridTextCellEditor,
        cellEditorParams: {
            pattern: '^\\d*(?:\\.\\d{0,2})?$',
            emptyStringIsNull: true,
        },
    },
    {
        field: 'meta_title',
        headerName: 'Meta Title',
        editable: true,
        sortable: true,
        filter: true,
        flex: 1,
        minWidth: 220,
    },
    {
        field: 'meta_description',
        headerName: 'Meta Description',
        editable: true,
        sortable: true,
        filter: true,
        flex: 1,
        minWidth: 220,
    },
    {
        field: 'is_active',
        headerName: 'Active',
        editable: true,
        sortable: true,
        filter: true,
        cellRenderer: 'agCheckboxCellRenderer',
        cellEditor: 'agCheckboxCellEditor',
        cellRendererParams: {
            disabled: (params) => !(params?.node?.isEditing?.() ?? false),
        },
        maxWidth: 110,
    },
    {
        field: 'variants_count',
        headerName: 'Variants',
        editable: false,
        sortable: true,
        filter: 'agNumberColumnFilter',
        maxWidth: 120,
    },
];
