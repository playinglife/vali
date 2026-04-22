function openImagePreviewDialog(imageUrl, onDelete) {
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

    const deleteButton = document.createElement('button');
    deleteButton.type = 'button';
    deleteButton.textContent = 'Delete';
    deleteButton.style.position = 'fixed';
    deleteButton.style.left = '50%';
    deleteButton.style.bottom = '2rem';
    deleteButton.style.transform = 'translateX(-50%)';
    deleteButton.style.zIndex = '100002';
    deleteButton.style.padding = '0.5rem 1rem';
    deleteButton.style.borderRadius = '6px';
    deleteButton.style.border = '1px solid rgba(0,0,0,0.25)';
    deleteButton.style.background = '#fff';
    deleteButton.style.cursor = 'pointer';
    deleteButton.style.fontWeight = '600';
    deleteButton.style.color = '#546A6F';
    deleteButton.addEventListener('click', async (event) => {
        event.stopPropagation();
        if (typeof onDelete !== 'function') {
            return;
        }
        deleteButton.disabled = true;
        try {
            const deleted = await onDelete();
            if (deleted) {
                overlay.remove();
            }
        } finally {
            deleteButton.disabled = false;
        }
    });

    overlay.appendChild(image);
    overlay.appendChild(deleteButton);
    document.body.appendChild(overlay);
}

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
    wrapper.style.alignItems = 'center';
    wrapper.style.gap = '0.25rem';
    wrapper.style.padding = '0.2rem 0';

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
    fileInput.style.display = 'none';

    addButton.addEventListener('click', () => {
        fileInput.click();
    });

    fileInput.addEventListener('change', async () => {
        const file = fileInput.files?.[0];
        if (!file) {
            return;
        }
        if (!variantId) {
            window.alert('Unable to upload image: missing ID.');
            return;
        }
        const formData = new FormData();
        formData.append('image', file);
        addButton.disabled = true;
        try {
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
                if (params?.api && params?.node) {
                    params.api.refreshCells({
                        rowNodes: [params.node],
                        columns: [params.column?.getColId?.() ?? 'image'],
                        force: true,
                    });
                }
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
        const img = document.createElement('img');
        img.src = imageUrl;
        img.alt = 'Variant image';
        img.style.cursor = 'zoom-in';
        img.style.width = '34px';
        img.style.height = '34px';
        img.style.objectFit = 'cover';
        img.style.borderRadius = '4px';
        img.style.border = '1px solid rgba(0,0,0,0.1)';
        img.addEventListener('click', async () => {
            openImagePreviewDialog(imageUrl, async () => {
                const imageId = entry?.id;
                if (!imageId) {
                    window.alert('Unable to delete image: missing image ID.');
                    return false;
                }
                try {
                    await axios.delete(`${params?.colDef?.cellRendererParams?.url.replace('{id}', String(variantId))}/${String(imageId)}`);
                    params.data.product_variant_images = (Array.isArray(params?.data?.product_variant_images)
                        ? params.data.product_variant_images
                        : []).filter((item) => Number(item?.id) !== Number(imageId));
                    if (params?.api && params?.node) {
                        params.api.refreshCells({
                            rowNodes: [params.node],
                            columns: [params.column?.getColId?.() ?? 'image'],
                            force: true,
                        });
                    }
                    return true;
                } catch (error) {
                    window.alert(error?.message || 'Failed to delete image.');
                    return false;
                }
            });
        });
        wrapper.appendChild(img);
    });

    return wrapper;
}
