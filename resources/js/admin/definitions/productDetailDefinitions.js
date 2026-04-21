import { GridValueCellEditor } from '../../ag-grid/cellEditors/GridValueCellEditor.js';

function openImagePreviewDialog(imageUrl) {
    if (!imageUrl) {
        return;
    }

    const overlay = document.createElement('div');
    overlay.style.position = 'fixed';
    overlay.style.inset = '0';
    overlay.style.zIndex = '100001';
    overlay.style.background = 'white';
    overlay.style.display = 'flex';
    overlay.style.alignItems = 'center';
    overlay.style.justifyContent = 'center';
    overlay.style.padding = '1rem';

    const image = document.createElement('img');
    image.src = String(imageUrl);
    image.alt = 'Variant full-size image';
    image.style.maxWidth = 'min(96vw, 1600px)';
    image.style.maxHeight = '96vh';
    image.style.width = 'auto';
    image.style.height = 'auto';
    image.style.objectFit = 'contain';
    image.style.cursor = 'zoom-out';
    image.style.borderRadius = '6px';
    image.style.boxShadow = '0 10px 40px rgba(0, 0, 0, 0.45)';
    image.addEventListener('click', () => {
        overlay.remove();
    });

    overlay.appendChild(image);
    document.body.appendChild(overlay);
}

function createVariantImagesRenderer(params) {
    const imageList = Array.isArray(params?.data?.product_variant_images)
        ? params.data.product_variant_images
        : [];
    const images = imageList;

    const wrapper = document.createElement('div');
    wrapper.style.display = 'flex';
    wrapper.style.alignItems = 'center';
    wrapper.style.gap = '0.25rem';
    wrapper.style.padding = '0.2rem 0';

    images.forEach((url) => {
        const img = document.createElement('img');
        img.src = String(url);
        img.alt = 'Variant image';
        img.style.cursor = 'zoom-in';
        img.style.width = '34px';
        img.style.height = '34px';
        img.style.objectFit = 'cover';
        img.style.borderRadius = '4px';
        img.style.border = '1px solid rgba(0,0,0,0.1)';
        img.addEventListener('click', () => {
            openImagePreviewDialog(url);
        });
        wrapper.appendChild(img);
    });

    return wrapper;
}

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
        editable: false,
        sortable: true,
        filter: true,
        minWidth: 160,
    },
    {
        field: 'price',
        headerName: 'Price',
        editable: false,
        sortable: true,
        filter: 'agNumberColumnFilter',
        minWidth: 120,
    },
    {
        field: 'stock_quantity',
        headerName: 'Stock',
        editable: false,
        sortable: true,
        filter: 'agNumberColumnFilter',
        minWidth: 110,
    },
    {
        field: 'discount_type',
        headerName: 'Discount Type',
        editable: false,
        sortable: true,
        filter: true,
        minWidth: 140,
    },
    {
        field: 'discount',
        headerName: 'Discount',
        editable: false,
        sortable: true,
        filter: 'agNumberColumnFilter',
        minWidth: 110,
    },
    {
        field: 'is_active',
        headerName: 'Active',
        editable: false,
        sortable: true,
        filter: true,
        minWidth: 110,
        valueFormatter: (params) => (params.value ? 'Yes' : 'No'),
    },
    {
        field: 'image',
        headerName: 'Image',
        editable: false,
        sortable: false,
        filter: false,
        minWidth: 140,
        cellRenderer: createVariantImagesRenderer,
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
        const field = `option_${option.id}_value_id`;

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
