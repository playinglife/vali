<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductOption;
use App\Models\ProductOptionValue;
use App\Models\ProductVariant;
use App\Models\Translation;
use Illuminate\Database\Seeder;

class ProductCatalogSeeder extends Seeder
{
    /**
     * 5 categories, 20 products with category links, per-product Size/Color options,
     * option values, and variants wired for the product grid filters.
     */
    public function run(): void
    {
        $categoryDefs = [
            ['name' => 'Business shirts', 'slug' => 'business-shirts'],
            ['name' => 'Casual', 'slug' => 'casual'],
            ['name' => 'Smart casual', 'slug' => 'smart-casual'],
            ['name' => "Women's line", 'slug' => 'womens-line'],
            ['name' => 'Accessories', 'slug' => 'accessories'],
        ];

        $categories = collect($categoryDefs)->map(function (array $def, int $index) {
            return Category::query()->create([
                'name' => $def['name'],
                'slug' => $def['slug'],
                'description' => 'Demo category for '.$def['name'].'.',
                'sort_order' => $index,
                'is_active' => true,
            ]);
        });

        $sizes = ['S', 'M', 'L', 'XL'];
        $colors = ['White', 'Navy', 'Sky Blue', 'Black'];

        for ($i = 1; $i <= 20; $i++) {
            $sku = sprintf('SH-%04d', $i);
            $slug = sprintf('shirt-model-%d', $i);

            $price = round(89 + ($i * 7) % 240 + ($i % 5) * 3, 2);
            $discountType = $i % 2 === 0 ? 'fix' : 'percentage';
            $discount = $discountType === 'fix'
                ? round(15 + ($i % 4) * 5, 2)
                : (float) (10 + ($i % 5) * 2);

            $shortTranslation = Translation::query()->create([
                'english' => 'Tailored shirt for professional wear — model '.$i.'.',
                'romanian' => 'Cămașă croită pentru ținută profesională — modelul '.$i.'.',
            ]);
            $longTranslation = Translation::query()->create([
                'english' => "Full description for shirt model {$i}.\nPremium fabric, tailored fit, available in multiple sizes and colors.",
                'romanian' => "Descriere completă pentru modelul de cămașă {$i}.\nMaterial premium, croi adaptat, disponibil în mai multe mărimi și culori.",
            ]);

            $product = Product::query()->create([
                'sku' => $sku,
                'name' => 'Shirt model '.$i,
                'slug' => $slug,
                'short_description_id' => $shortTranslation->id,
                'description_id' => $longTranslation->id,
                'price' => $price,
                'discount_type' => $discountType,
                'discount' => $discount,
                'cost' => round($price * 0.45, 2),
                'is_active' => true,
                'is_featured' => $i % 4 === 0,
                'weight' => round(0.28 + ($i % 10) * 0.01, 3),
                'meta_title' => 'Shirt model '.$i,
                'meta_description' => 'Order shirt model '.$i.' — business quality.',
            ]);

            $primary = $categories[$i % 5];
            $secondary = $categories[($i + 2) % 5];
            $pivot = [
                $primary->id => ['sort_order' => 0],
            ];
            if ($i % 3 === 0) {
                $pivot[$secondary->id] = ['sort_order' => 1];
            }
            $product->categories()->sync($pivot);

            $sizeOption = ProductOption::query()->create([
                'product_id' => $product->id,
                'name' => 'Size',
                'sort_order' => 0,
            ]);
            $colorOption = ProductOption::query()->create([
                'product_id' => $product->id,
                'name' => 'Color',
                'sort_order' => 1,
            ]);

            $sizeValues = collect($sizes)->map(function (string $label, int $order) use ($sizeOption) {
                return ProductOptionValue::query()->create([
                    'product_option_id' => $sizeOption->id,
                    'value' => $label,
                    'price_adjustment_type' => 'fix',
                    'price_adjustment' => (float) ($order * 4),
                    'sort_order' => $order,
                ]);
            });

            $colorValues = collect($colors)->map(function (string $label, int $order) use ($colorOption) {
                return ProductOptionValue::query()->create([
                    'product_option_id' => $colorOption->id,
                    'value' => $label,
                    'price_adjustment_type' => 'percentage',
                    'price_adjustment' => (float) (2 + $order * 1.5),
                    'sort_order' => $order,
                ]);
            });

            $defaultVariant = $product->variants()->where('sku', $product->sku)->firstOrFail();
            $defaultVariant->update([
                'stock_quantity' => 10 + $i,
            ]);

            $sizeForDefault = $sizeValues[($i - 1) % $sizeValues->count()];
            $colorForDefault = $colorValues[($i + 1) % $colorValues->count()];
            $defaultVariant->optionValues()->sync([
                $sizeForDefault->id,
                $colorForDefault->id,
            ]);

            if ($i % 2 === 0) {
                $extra = ProductVariant::query()->create([
                    'product_id' => $product->id,
                    'sku' => $sku.'-ALT',
                    'price' => null,
                    'discount_type' => null,
                    'discount' => null,
                    'stock_quantity' => 5 + ($i % 7),
                    'weight' => $product->weight,
                    'is_active' => true,
                ]);

                $sizeAlt = $sizeValues[($i) % $sizeValues->count()];
                $colorAlt = $colorValues[($i * 2) % $colorValues->count()];
                $extra->optionValues()->sync([
                    $sizeAlt->id,
                    $colorAlt->id,
                ]);
            }
        }
    }
}
