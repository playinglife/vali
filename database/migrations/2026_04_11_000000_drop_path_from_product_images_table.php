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
        if (Schema::hasTable('product_images') && Schema::hasColumn('product_images', 'path')) {
            Schema::table('product_images', function (Blueprint $table) {
                $table->dropColumn('path');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('product_images') && ! Schema::hasColumn('product_images', 'path')) {
            Schema::table('product_images', function (Blueprint $table) {
                $table->string('path')->after('product_id');
            });
        }
    }
};
