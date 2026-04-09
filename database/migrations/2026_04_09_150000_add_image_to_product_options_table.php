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
        if (! Schema::hasColumn('product_options', 'image')) {
            Schema::table('product_options', function (Blueprint $table) {
                $table->string('image')->nullable()->after('show_on_products');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('product_options', 'image')) {
            Schema::table('product_options', function (Blueprint $table) {
                $table->dropColumn('image');
            });
        }
    }
};
