@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@push('styles')
    {{-- @vite('resources/scss/ag-grid.scss') --}}
@endpush

@section('content')
    <div class="admin-dashboard">
        <div class="admin-header">
            <span class="admin-header-title">Product Management</span>
            <form method="POST" action="{{ route('admin.logout') }}">
                @csrf
                <button type="submit">Logout</button>
            </form>
        </div>

        @php
            $gridRows = $products->map(fn ($product) => [
                'id' => $product->id,
                'sku' => $product->sku,
                'name' => $product->name,
                'slug' => $product->slug,
                'price' => (float) $product->price,
                'is_active' => $product->is_active ? 'Yes' : 'No',
                'variants_count' => $product->Variants->count(),
            ])->values();
        @endphp

        <div class="ag-theme-quartz admin-products-grid" data-admin-products-grid id="admin-products-grid" oncontextmenu="return false;"></div>

    </div>
@endsection



<!-- SCRIPTS -->
<script id="transferDataProducts" type="application/json">{!! $gridRows->toJson() !!}</script>
<script>
    window.transferData = window.transferData || {};
    window.transferData.products = JSON.parse(
        document.getElementById('transferDataProducts')?.textContent || '[]'
    );
</script>
<script src="https://cdn.jsdelivr.net/npm/ag-grid-community/dist/ag-grid-community.min.js"></script>
<script type="module">
    import { productColumnDefs } from "{{ asset('js/admin/definitions/product.js') }}";
    import { createAgGridCommon } from "{{ Vite::asset('resources/js/AgGridCommon.js') }}";

    (function () {
        function initGrid() {
            var gridEl = document.getElementById('admin-products-grid');
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
                            parent_id: item.parent_id ?? null, // null for root (tree data)
                            description: item.description || '',
                            created_at: item.created_at || '',
                            updated_at: item.updated_at || '',
                        }
                    },
                }
            });
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initGrid);
        } else {
            initGrid();
        }
    })();
</script>



<!-- STYLES -->
@once
    <style>
        .admin-dashboard {
            padding: var(--padding-small);
            gap: var(--gap-small);
            display: flex;
            flex-direction: column;
            height: 100%;
        }

        .admin-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .admin-products-grid {
            width: 100%;
            height: 100%;
            flex: 1;
        }

        .admin-header-title {
            font-size: var(--text-size-small);
            font-weight: 700;
            color: var(--color-text-light);
        }
    </style>
@endonce
