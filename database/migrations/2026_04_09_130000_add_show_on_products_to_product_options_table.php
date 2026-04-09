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
        if (! Schema::hasColumn('product_options', 'show_on_products')) {
            Schema::table('product_options', function (Blueprint $table) {
                $table->boolean('show_on_products')->default(true)->after('name');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('product_options', 'show_on_products')) {
            Schema::table('product_options', function (Blueprint $table) {
                $table->dropColumn('show_on_products');
            });
        }
    }
};
