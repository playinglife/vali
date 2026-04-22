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
        Schema::table('order_items', function (Blueprint $table) {
            $table->decimal('price', 12, 2)->default(0)->after('quantity');
            $table->enum('discount_type', ['fixed', 'percentage'])->nullable()->after('price');
            $table->decimal('discount', 10, 2)->nullable()->after('discount_type');
            $table->string('currency', 3)->default('RON')->after('discount');
        });
        DB::statement('UPDATE order_items SET price = unit_price');
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn(['unit_price', 'line_total']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->decimal('unit_price', 12, 2)->default(0)->after('quantity');
            $table->decimal('line_total', 12, 2)->default(0)->after('unit_price');
        });
        DB::statement('UPDATE order_items SET unit_price = price, line_total = price * quantity');
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn(['price', 'discount_type', 'discount', 'currency']);
        });
    }
};
