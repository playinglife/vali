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
        if (Schema::hasTable('product_options')) {
            if (! Schema::hasColumn('product_options', 'type')) {
                Schema::table('product_options', function (Blueprint $table) {
                    $table->enum('type', ['text', 'icon', 'image'])->default('text')->after('show_on_products');
                });
            }

            if (Schema::hasColumn('product_options', 'image')) {
                Schema::table('product_options', function (Blueprint $table) {
                    $table->dropColumn('image');
                });
            }
        }

        if (Schema::hasTable('product_option_values') && ! Schema::hasColumn('product_option_values', 'icon')) {
            Schema::table('product_option_values', function (Blueprint $table) {
                $table->string('icon')->nullable()->after('value');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('product_option_values') && Schema::hasColumn('product_option_values', 'icon')) {
            Schema::table('product_option_values', function (Blueprint $table) {
                $table->dropColumn('icon');
            });
        }

        if (Schema::hasTable('product_options')) {
            if (Schema::hasColumn('product_options', 'type')) {
                Schema::table('product_options', function (Blueprint $table) {
                    $table->dropColumn('type');
                });
            }

            if (! Schema::hasColumn('product_options', 'image')) {
                Schema::table('product_options', function (Blueprint $table) {
                    $table->string('image')->nullable()->after('show_on_products');
                });
            }
        }
    }
};
