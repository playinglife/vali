<?php

namespace App\Features\Admin\Controllers;

use App\Features\Admin\Resources\OptionResource;
use App\Features\Admin\Resources\ProductResource;
use App\Features\Admin\Resources\VariantResource;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductOption;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $products = Product::query()
            ->with([
                'ProductImages',
                'OptionValues.Option',
                'Variants.VariantImages',
                'Variants.Values',
            ])
            ->orderByDesc('id')
            ->get();

        return view('pages.admin.dashboard', [
            'products' => ProductResource::collection($products)->resolve(),
        ]);
    }

    public function productDetail(Product $product): View
    {
        $product->load([
            'OptionValues.Option',
            'Variants.Values.Option',
            'Variants.VariantImages',
        ]);
        $options = ProductOption::query()->with('Values')->orderBy('sort_order')->get();

        return view('pages.admin.product-detail', [
            'product' => ProductResource::make($product)->resolve(),
            'variants' => VariantResource::collection($product->Variants)->resolve(),
            'options' => OptionResource::collection($options)->resolve(),
        ]);
    }
}
