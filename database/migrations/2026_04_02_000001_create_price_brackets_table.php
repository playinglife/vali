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
        Schema::create('price_brackets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('product_variant_id')->nullable()->constrained()->cascadeOnDelete();
            $table->unsignedInteger('start_quantity');
            $table->unsignedInteger('end_quantity')->nullable();
            $table->decimal('price', 10, 2);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        $driver = Schema::getConnection()->getDriverName();
        if (in_array($driver, ['mysql', 'pgsql'], true)) {
            DB::statement('ALTER TABLE price_brackets ADD CONSTRAINT price_brackets_scope_xor CHECK (
                (product_id IS NOT NULL AND product_variant_id IS NULL)
                OR (product_id IS NULL AND product_variant_id IS NOT NULL)
            )');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('price_brackets');
    }
};
