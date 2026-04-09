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
        if (! Schema::hasColumn('product_option_values', 'image')) {
            Schema::table('product_option_values', function (Blueprint $table) {
                $table->string('image')->nullable()->after('value');
            });
        }

        // Optional transition: copy option-level image to each option value if set.
        if (Schema::hasColumn('product_options', 'image')) {
            DB::table('product_option_values as pov')
                ->join('product_options as po', 'po.id', '=', 'pov.product_option_id')
                ->whereNull('pov.image')
                ->whereNotNull('po.image')
                ->update(['pov.image' => DB::raw('po.image')]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('product_option_values', 'image')) {
            Schema::table('product_option_values', function (Blueprint $table) {
                $table->dropColumn('image');
            });
        }
    }
};
