<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Earlier builds used `description_translation_id` on product_variants; align with `description_id`.
     */
    public function up(): void
    {
        if (! Schema::hasTable('product_variants')) {
            return;
        }
        if (! Schema::hasColumn('product_variants', 'description_translation_id')) {
            return;
        }
        if (Schema::hasColumn('product_variants', 'description_id')) {
            return;
        }

        Schema::table('product_variants', function (Blueprint $table) {
            $table->dropForeign(['description_translation_id']);
        });

        Schema::table('product_variants', function (Blueprint $table) {
            $table->renameColumn('description_translation_id', 'description_id');
        });

        Schema::table('product_variants', function (Blueprint $table) {
            $table->foreign('description_id')
                ->references('id')
                ->on('translations')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('product_variants')) {
            return;
        }
        if (! Schema::hasColumn('product_variants', 'description_id')) {
            return;
        }
        if (Schema::hasColumn('product_variants', 'description_translation_id')) {
            return;
        }

        Schema::table('product_variants', function (Blueprint $table) {
            $table->dropForeign(['description_id']);
        });

        Schema::table('product_variants', function (Blueprint $table) {
            $table->renameColumn('description_id', 'description_translation_id');
        });

        Schema::table('product_variants', function (Blueprint $table) {
            $table->foreign('description_translation_id')
                ->references('id')
                ->on('translations')
                ->nullOnDelete();
        });
    }
};
