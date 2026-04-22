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
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropForeign(['product_variant_id']);
            $table->renameColumn('product_variant_id', 'variant_id');
        });
        Schema::table('order_items', function (Blueprint $table) {
            $table->foreign('variant_id')->references('id')->on('product_variants')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropForeign(['variant_id']);
            $table->renameColumn('variant_id', 'product_variant_id');
        });
        Schema::table('order_items', function (Blueprint $table) {
            $table->foreign('product_variant_id')->references('id')->on('product_variants')->nullOnDelete();
        });
    }
};
