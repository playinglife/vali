import { GridValueCellEditor } from '../../ag-grid/cellEditors/GridValueCellEditor.js';
import { GridTextCellEditor } from '../../ag-grid/cellEditors/GridTextCellEditor.js';
import { GridSelectCellRenderer } from '../../ag-grid/cellRenderers/GridSelectCellRenderer.js';
import { GridImageCellRenderer } from '../../ag-grid/cellRenderers/GridImageCellRenderer.js';
import { GridSelectCellEditor } from '../../ag-grid/cellEditors/GridSelectCellEditor.js';

export const productDetailBaseDefinitions = [
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
        minWidth: 160,
    },
    {
        field: 'price',
        headerName: 'Price',
        editable: true,
        sortable: true,
        filter: 'agNumberColumnFilter',
        minWidth: 120,
        cellEditor: GridTextCellEditor,
        cellEditorParams: {
            pattern: '^\\d*(?:\\.\\d{0,2})?$',
            emptyStringIsNull: true,
        },
    },
    {
        field: 'stock_quantity',
        headerName: 'Stock',
        editable: true,
        sortable: true,
        filter: 'agNumberColumnFilter',
        minWidth: 110,
        cellEditor: GridTextCellEditor,
        cellEditorParams: {
            pattern: '^\\d*$',
            emptyStringIsNull: true,
        },
    },
    {
        field: 'discount_type',
        headerName: 'Discount Type',
        editable: true,
        sortable: true,
        filter: true,
        minWidth: 140,
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
        minWidth: 110,
        cellEditor: GridTextCellEditor,
        cellEditorParams: {
            pattern: '^\\d*(?:\\.\\d{0,2})?$',
            emptyStringIsNull: true,
        },
    },
    {
        field: 'is_active',
        headerName: 'Active',
        editable: true,
        sortable: true,
        filter: true,
        minWidth: 110,
        cellRenderer: 'agCheckboxCellRenderer',
        cellEditor: 'agCheckboxCellEditor',
        cellRendererParams: {
            disabled: (params) => !(params?.node?.isEditing?.() ?? false),
        },
    },
    {
        field: 'image',
        headerName: 'Image',
        editable: false,
        sortable: false,
        filter: false,
        minWidth: 140,
        cellRenderer: GridImageCellRenderer,
        cellRendererParams: {
            idColumn: 'id',
            url: '/admin/products/' + String(window.transferData?.product?.id) + '/variants/{id}/images',
        },
    }
];

function createOptionRadioRenderer(values, optionId) {
    const normalizedValues = Array.isArray(values) ? values : [];

    return (params) => {
        const selected = params.value == null ? '' : String(params.value);
        const wrapper = document.createElement('div');
        wrapper.style.display = 'flex';
        wrapper.style.flexWrap = 'wrap';
        wrapper.style.gap = '0.5rem';

        normalizedValues.forEach((value) => {
            const id = String(value.id ?? '');
            const label = value.value ?? '';
            const item = document.createElement('label');
            item.style.display = 'inline-flex';
            item.style.alignItems = 'center';
            item.style.gap = '0.25rem';
            item.style.fontSize = '0.8rem';

            const input = document.createElement('input');
            input.type = 'radio';
            input.disabled = true;
            input.name = `view-option-${optionId}-${params.node?.id ?? ''}`;
            input.value = id;
            input.checked = id === selected;

            const text = document.createElement('span');
            text.textContent = String(label);

            item.appendChild(input);
            item.appendChild(text);
            wrapper.appendChild(item);
        });

        return wrapper;
    };
}

export function buildProductDetailDefinitions(options = []) {
    const optionColumns = (Array.isArray(options) ? options : []).map((option) => {
        const values = Array.isArray(option.values) ? option.values : [];
        const field = option.name;

        return {
            field,
            headerName: option.name || `Option ${option.id}`,
            editable: true,
            sortable: false,
            filter: false,
            minWidth: 220,
            cellEditor: GridValueCellEditor,
            cellEditorParams: {
                optionId: option.id,
                values,
            },
            cellRenderer: createOptionRadioRenderer(values, option.id),
        };
    });

    return [
        ...productDetailBaseDefinitions,
        ...optionColumns,
    ];
}
