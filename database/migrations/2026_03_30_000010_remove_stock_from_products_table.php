<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Inventory lives on product_variants only; products may still have these columns from older migrations.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'stock_quantity')) {
                $table->dropColumn(['stock_quantity', 'track_stock']);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (! Schema::hasColumn('products', 'stock_quantity')) {
                $table->unsignedInteger('stock_quantity')->default(0);
                $table->boolean('track_stock')->default(true);
            }
        });
    }
};
