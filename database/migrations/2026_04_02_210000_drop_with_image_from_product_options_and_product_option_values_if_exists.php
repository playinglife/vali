<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Removes with_image from product_options / product_option_values when a prior migration
     * had added them; swatch paths live only on product_variant_option_values.with_image.
     */
    public function up(): void
    {
        if (Schema::hasColumn('product_option_values', 'with_image')) {
            Schema::table('product_option_values', function (Blueprint $table) {
                $table->dropColumn('with_image');
            });
        }

        if (Schema::hasColumn('product_options', 'with_image')) {
            Schema::table('product_options', function (Blueprint $table) {
                $table->dropColumn('with_image');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasColumn('product_options', 'with_image')) {
            Schema::table('product_options', function (Blueprint $table) {
                $table->boolean('with_image')->default(false)->after('sort_order');
            });
        }

        if (! Schema::hasColumn('product_option_values', 'with_image')) {
            Schema::table('product_option_values', function (Blueprint $table) {
                $table->string('with_image')->nullable()->after('sort_order');
            });
        }
    }
};
