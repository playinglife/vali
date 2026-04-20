<?php

namespace App\Features\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Product;
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
            'products' => $products,
        ]);
    }
}
