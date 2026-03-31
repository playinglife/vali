<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasTable('product_variants')) {
            return;
        }

        if (! Schema::hasColumn('product_variants', 'track_stock')) {
            return;
        }

        DB::table('product_variants')->where('track_stock', false)->update(['stock_quantity' => null]);

        Schema::table('product_variants', function (Blueprint $table) {
            $table->dropColumn('track_stock');
        });

        Schema::table('product_variants', function (Blueprint $table) {
            $table->unsignedInteger('stock_quantity')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('product_variants') || Schema::hasColumn('product_variants', 'track_stock')) {
            return;
        }

        Schema::table('product_variants', function (Blueprint $table) {
            $table->boolean('track_stock')->default(true);
        });

        DB::statement(
            'UPDATE product_variants SET track_stock = CASE WHEN stock_quantity IS NULL THEN 0 ELSE 1 END'
        );

        DB::table('product_variants')->whereNull('stock_quantity')->update(['stock_quantity' => 0]);

        Schema::table('product_variants', function (Blueprint $table) {
            $table->unsignedInteger('stock_quantity')->default(0)->nullable(false)->change();
        });
    }
};
