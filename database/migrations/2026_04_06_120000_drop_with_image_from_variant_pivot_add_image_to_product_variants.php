<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Swatch paths live on product_variants.image (relative to the public disk).
     */
    public function up(): void
    {
        if (Schema::hasColumn('product_variant_option_values', 'with_image')) {
            Schema::table('product_variant_option_values', function (Blueprint $table) {
                $table->dropColumn('with_image');
            });
        }

        if (! Schema::hasColumn('product_variants', 'image')) {
            Schema::table('product_variants', function (Blueprint $table) {
                $table->string('image')->nullable()->after('barcode');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('product_variants', 'image')) {
            Schema::table('product_variants', function (Blueprint $table) {
                $table->dropColumn('image');
            });
        }

        if (! Schema::hasColumn('product_variant_option_values', 'with_image')) {
            Schema::table('product_variant_option_values', function (Blueprint $table) {
                $table->string('with_image')->nullable()->after('product_option_value_id');
            });
        }
    }
};
