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
        Schema::create('product_variant_option_values', function (Blueprint $table) {
            $table->engine('InnoDB');
            $table->id();
            $table->unsignedBigInteger('product_variant_id');
            $table->unsignedBigInteger('product_option_value_id');
            $table->timestamps();

            $table->unique(
                ['product_variant_id', 'product_option_value_id'],
                'pvov_variant_optval_unique'
            );
        });

        Schema::table('product_variant_option_values', function (Blueprint $table) {
            $table->foreign('product_variant_id', 'pvov_variant_fk')
                ->references('id')
                ->on('product_variants')
                ->cascadeOnDelete();

            $table->foreign('product_option_value_id', 'pvov_optval_fk')
                ->references('id')
                ->on('product_option_values')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_variant_option_values');
    }
};
