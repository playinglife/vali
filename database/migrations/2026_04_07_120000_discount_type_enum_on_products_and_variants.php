<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * `discount_type` is constrained to fixed | percentage (legacy `fix` migrated to `fixed`).
     */
    public function up(): void
    {
        foreach (['products', 'product_variants'] as $table) {
            if (! Schema::hasTable($table) || ! Schema::hasColumn($table, 'discount_type')) {
                continue;
            }

            DB::table($table)->where('discount_type', 'fix')->update(['discount_type' => 'fixed']);

            DB::table($table)
                ->whereNotNull('discount_type')
                ->whereNotIn('discount_type', ['fixed', 'percentage'])
                ->update(['discount_type' => null]);

            Schema::table($table, function (Blueprint $blueprint) {
                $blueprint->enum('discount_type', ['fixed', 'percentage'])->nullable()->change();
            });
        }
    }

    public function down(): void
    {
        foreach (['products', 'product_variants'] as $table) {
            if (! Schema::hasTable($table) || ! Schema::hasColumn($table, 'discount_type')) {
                continue;
            }

            Schema::table($table, function (Blueprint $blueprint) {
                $blueprint->string('discount_type')->nullable()->change();
            });

            DB::table($table)->where('discount_type', 'fixed')->update(['discount_type' => 'fix']);
        }
    }
};
