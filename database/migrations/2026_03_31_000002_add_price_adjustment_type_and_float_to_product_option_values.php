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
        if (! Schema::hasColumn('product_option_values', 'price_adjustment_type')) {
            Schema::table('product_option_values', function (Blueprint $table) {
                $table->string('price_adjustment_type')->nullable()->after('value');
            });

            DB::table('product_option_values')
                ->whereNull('price_adjustment_type')
                ->update(['price_adjustment_type' => 'fix']);

            Schema::table('product_option_values', function (Blueprint $table) {
                $table->float('price_adjustment')->default(0)->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('product_option_values', 'price_adjustment_type')) {
            Schema::table('product_option_values', function (Blueprint $table) {
                $table->dropColumn('price_adjustment_type');
            });
        }

        if (Schema::hasColumn('product_option_values', 'price_adjustment')) {
            Schema::table('product_option_values', function (Blueprint $table) {
                $table->decimal('price_adjustment', 10, 2)->default(0)->change();
            });
        }
    }
};
