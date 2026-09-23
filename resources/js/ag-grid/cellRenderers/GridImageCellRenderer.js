export function GridImageCellRenderer(params) {
    //console.log(params);
    const variantId = params?.data?.[params?.colDef?.cellRendererParams?.idColumn];
    const imageList = Array.isArray(params?.data?.product_variant_images)
        ? params.data.product_variant_images
        : [];
    const images = imageList
        .map((entry) => {
            if (typeof entry === 'string') {
                return { id: null, image: entry };
            }
            if (entry && typeof entry === 'object') {
                return {
                    id: entry.id ?? null,
                    image: entry.image ?? null,
                };
            }
            return null;
        })
        .filter((entry) => typeof entry?.image === 'string' && entry.image.length > 0);

    const wrapper = document.createElement('div');
    wrapper.style.display = 'flex';
    wrapper.style.flexWrap = 'wrap';
    wrapper.style.alignItems = 'center';
    wrapper.style.gap = '0.25rem';
    wrapper.style.padding = '0.2rem 0';

    const refreshImageCell = () => {
        const colId = params.column?.getColId?.() ?? 'image';
        if (params?.api && params?.node) {
            params.api.refreshCells({
                rowNodes: [params.node],
                columns: [colId],
                force: true,
            });
            if (typeof params.api.autoSizeColumns === 'function') {
                params.api.autoSizeColumns([colId], false);
            }
            if (typeof params.api.resetRowHeights === 'function') {
                params.api.resetRowHeights();
            }
        }
    };

    const addButton = document.createElement('button');
    addButton.type = 'button';
    addButton.textContent = '+';
    addButton.title = 'Add image';
    addButton.style.width = '22px';
    addButton.style.height = '22px';
    addButton.style.minWidth = '22px';
    addButton.style.border = '1px solid rgba(0,0,0,0.2)';
    addButton.style.borderRadius = '4px';
    addButton.style.background = '#fff';
    addButton.style.cursor = 'pointer';
    addButton.style.fontWeight = '700';
    addButton.style.lineHeight = '1';
    addButton.style.color = '#546A6F';
    addButton.style.fontSize = '1em';
    addButton.style.padding = '0';

    const fileInput = document.createElement('input');
    fileInput.type = 'file';
    fileInput.accept = 'image/*';
    fileInput.multiple = true;
    fileInput.style.display = 'none';

    addButton.addEventListener('click', (event) => {
        event.preventDefault();
        event.stopPropagation();
        fileInput.click();
    });

    fileInput.addEventListener('change', async () => {
        const files = Array.from(fileInput.files || []);
        if (files.length === 0) {
            return;
        }
        if (!variantId) {
            window.alert('Unable to upload image: missing ID.');
            return;
        }
        addButton.disabled = true;
        try {
            let added = false;
            for (const file of files) {
                const formData = new FormData();
                formData.append('image', file);
                const response = await axios.post(
                    params?.colDef?.cellRendererParams?.url.replace('{id}', String(variantId)),
                    formData,
                    { headers: { 'Content-Type': 'multipart/form-data' } }
                );
                const imageUrl = response?.data?.image;
                if (imageUrl) {
                    const current = Array.isArray(params?.data?.product_variant_images)
                        ? params.data.product_variant_images
                        : [];
                    params.data.product_variant_images = [
                        ...current,
                        {
                            id: response?.data?.variant_image_id ?? null,
                            image: imageUrl,
                        },
                    ];
                    added = true;
                }
            }
            if (added) {
                refreshImageCell();
            }
        } catch (error) {
            window.alert(error?.message || 'Failed to upload image.');
        } finally {
            addButton.disabled = false;
            fileInput.value = '';
        }
    });

    wrapper.appendChild(addButton);
    wrapper.appendChild(fileInput);

    images.forEach((entry) => {
        const imageUrl = String(entry.image);
        const item = document.createElement('div');
        item.style.position = 'relative';
        item.style.display = 'inline-flex';

        const img = document.createElement('img');
        img.src = imageUrl;
        img.alt = 'Variant image';
        img.style.cursor = 'pointer';
        img.style.width = '34px';
        img.style.height = '34px';
        img.style.objectFit = 'cover';
        img.style.borderRadius = '4px';
        img.style.border = '1px solid rgba(0,0,0,0.1)';
        img.addEventListener('click', (event) => {
            event.preventDefault();
            event.stopPropagation();
            window.open(imageUrl, '_blank', 'noopener,noreferrer');
        });

        const deleteButton = document.createElement('button');
        deleteButton.type = 'button';
        deleteButton.textContent = '×';
        deleteButton.title = 'Delete image';
        deleteButton.style.position = 'absolute';
        deleteButton.style.top = '-4px';
        deleteButton.style.right = '-4px';
        deleteButton.style.width = '14px';
        deleteButton.style.height = '14px';
        deleteButton.style.padding = '0';
        deleteButton.style.lineHeight = '1';
        deleteButton.style.fontSize = '10px';
        deleteButton.style.border = '1px solid rgba(0,0,0,0.25)';
        deleteButton.style.borderRadius = '50%';
        deleteButton.style.background = '#fff';
        deleteButton.style.cursor = 'pointer';
        deleteButton.style.color = '#546A6F';
        deleteButton.addEventListener('click', async (event) => {
            event.stopPropagation();
            const imageId = entry?.id;
            if (!imageId) {
                window.alert('Unable to delete image: missing image ID.');
                return;
            }
            deleteButton.disabled = true;
            try {
                await axios.delete(`${params?.colDef?.cellRendererParams?.url.replace('{id}', String(variantId))}/${String(imageId)}`);
                params.data.product_variant_images = (Array.isArray(params?.data?.product_variant_images)
                    ? params.data.product_variant_images
                    : []).filter((item) => Number(item?.id) !== Number(imageId));
                if (params?.api && params?.node) {
                    refreshImageCell();
                }
            } catch (error) {
                window.alert(error?.message || 'Failed to delete image.');
            } finally {
                deleteButton.disabled = false;
            }
        });

        item.appendChild(img);
        item.appendChild(deleteButton);
        wrapper.appendChild(item);
    });

    return wrapper;
}
