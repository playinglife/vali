<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasColumn('products', 'compare_at_price')) {
            Schema::table('products', function (Blueprint $table) {
                $table->dropColumn('compare_at_price');
            });
        }
        if (! Schema::hasColumn('products', 'discount_type')) {
            Schema::table('products', function (Blueprint $table) {
                $table->string('discount_type')->nullable()->after('price');
                $table->decimal('discount', 10, 2)->nullable()->after('discount_type');
            });
        }

        if (Schema::hasColumn('product_variants', 'compare_at_price')) {
            Schema::table('product_variants', function (Blueprint $table) {
                $table->dropColumn('compare_at_price');
            });
        }
        if (! Schema::hasColumn('product_variants', 'discount_type')) {
            Schema::table('product_variants', function (Blueprint $table) {
                $table->string('discount_type')->nullable()->after('price');
                $table->decimal('discount', 10, 2)->nullable()->after('discount_type');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('products', 'discount_type')) {
            Schema::table('products', function (Blueprint $table) {
                $table->dropColumn(['discount_type', 'discount']);
            });
        }
        if (! Schema::hasColumn('products', 'compare_at_price')) {
            Schema::table('products', function (Blueprint $table) {
                $table->decimal('compare_at_price', 10, 2)->nullable()->after('price');
            });
        }

        if (Schema::hasColumn('product_variants', 'discount_type')) {
            Schema::table('product_variants', function (Blueprint $table) {
                $table->dropColumn(['discount_type', 'discount']);
            });
        }
        if (! Schema::hasColumn('product_variants', 'compare_at_price')) {
            Schema::table('product_variants', function (Blueprint $table) {
                $table->decimal('compare_at_price', 10, 2)->nullable()->after('price');
            });
        }
    }
};
