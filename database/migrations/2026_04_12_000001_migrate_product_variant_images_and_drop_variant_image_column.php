<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

return new class extends Migration
{
    /**
     * Move existing variant image paths / legacy product/variants/{id}.* files into
     * {@see \App\Models\ProductVariantImage::STORAGE_PATH}/{row_id}.{ext}, then drop product_variants.image.
     */
    public function up(): void
    {
        if (! Schema::hasTable('product_variant_images') || ! Schema::hasTable('product_variants')) {
            return;
        }

        $disk = Storage::disk('public');
        $disk->makeDirectory(\App\Models\ProductVariantImage::STORAGE_PATH);

        $legacyExts = ['png', 'jpg', 'jpeg', 'webp'];

        $hasImageColumn = Schema::hasColumn('product_variants', 'image');
        $columns = $hasImageColumn ? ['id', 'image'] : ['id'];
        $variants = DB::table('product_variants')->select($columns)->orderBy('id')->get();

        foreach ($variants as $row) {
            $variantId = (int) $row->id;
            $columnPath = $hasImageColumn && isset($row->image) && is_string($row->image)
                ? trim($row->image)
                : '';
            $sourcePath = null;
            $sourceExt = null;

            if ($columnPath !== '') {
                if ($disk->exists($columnPath)) {
                    $sourcePath = $columnPath;
                    $sourceExt = strtolower((string) pathinfo($columnPath, PATHINFO_EXTENSION));
                }
            }

            if ($sourcePath === null) {
                foreach ($legacyExts as $ext) {
                    $legacy = 'product/variants/'.$variantId.'.'.$ext;
                    if ($disk->exists($legacy)) {
                        $sourcePath = $legacy;
                        $sourceExt = $ext;

                        break;
                    }
                }
            }

            if ($sourcePath === null) {
                continue;
            }

            if ($sourceExt === 'jpeg') {
                $sourceExt = 'jpg';
            }
            if ($sourceExt === '' || ! in_array($sourceExt, \App\Models\ProductVariantImage::FILENAME_EXTENSIONS, true)) {
                $sourceExt = 'jpg';
            }

            $now = now();
            $imageId = DB::table('product_variant_images')->insertGetId([
                'product_variant_id' => $variantId,
                'sort_order' => 0,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            $dest = \App\Models\ProductVariantImage::STORAGE_PATH.'/'.$imageId.'.'.$sourceExt;
            $disk->copy($sourcePath, $dest);
        }

        if (Schema::hasColumn('product_variants', 'image')) {
            Schema::table('product_variants', function (Blueprint $table) {
                $table->dropColumn('image');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('product_variants') && ! Schema::hasColumn('product_variants', 'image')) {
            Schema::table('product_variants', function (Blueprint $table) {
                $table->string('image')->nullable()->after('barcode');
            });
        }

        if (Schema::hasTable('product_variant_images')) {
            DB::table('product_variant_images')->truncate();
        }
    }
};
