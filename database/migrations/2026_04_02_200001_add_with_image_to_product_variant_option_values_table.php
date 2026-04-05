<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Nullable path on the public disk for this variant↔option-value link (swatch).
     * Null = plain select for that option dimension; non-null (including empty string) = image-style picker for that option.
     */
    public function up(): void
    {
        Schema::table('product_variant_option_values', function (Blueprint $table) {
            $table->string('with_image')->nullable()->after('product_option_value_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_variant_option_values', function (Blueprint $table) {
            $table->dropColumn('with_image');
        });
    }
};
