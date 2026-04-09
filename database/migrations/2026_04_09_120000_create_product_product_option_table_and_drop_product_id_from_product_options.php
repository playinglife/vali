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
        if (! Schema::hasTable('product_product_option')) {
            Schema::create('product_product_option', function (Blueprint $table) {
                $table->id();
                $table->foreignId('product_id')->constrained()->cascadeOnDelete();
                $table->foreignId('product_option_id')->constrained()->cascadeOnDelete();
                $table->unsignedInteger('sort_order')->default(0);
                $table->timestamps();

                $table->unique(['product_id', 'product_option_id'], 'ppo_product_option_unique');
                $table->index(['product_id', 'sort_order'], 'ppo_product_sort_order_idx');
            });
        }

        if (Schema::hasTable('product_options') && Schema::hasColumn('product_options', 'product_id')) {
            DB::table('product_options')
                ->select(['id', 'product_id', 'sort_order', 'created_at', 'updated_at'])
                ->orderBy('id')
                ->chunkById(500, function ($rows) {
                    $payload = [];
                    foreach ($rows as $row) {
                        if ($row->product_id === null) {
                            continue;
                        }

                        $payload[] = [
                            'product_id' => (int) $row->product_id,
                            'product_option_id' => (int) $row->id,
                            'sort_order' => (int) ($row->sort_order ?? 0),
                            'created_at' => $row->created_at,
                            'updated_at' => $row->updated_at,
                        ];
                    }

                    if ($payload !== []) {
                        DB::table('product_product_option')->upsert(
                            $payload,
                            ['product_id', 'product_option_id'],
                            ['sort_order', 'updated_at']
                        );
                    }
                });

            Schema::table('product_options', function (Blueprint $table) {
                $table->dropConstrainedForeignId('product_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('product_options')) {
            return;
        }

        if (! Schema::hasColumn('product_options', 'product_id')) {
            Schema::table('product_options', function (Blueprint $table) {
                $table->foreignId('product_id')->nullable()->after('id')->constrained()->cascadeOnDelete();
            });
        }

        if (Schema::hasTable('product_product_option')) {
            DB::table('product_product_option')
                ->select(['product_id', 'product_option_id'])
                ->orderBy('id')
                ->chunkById(500, function ($rows) {
                    foreach ($rows as $row) {
                        DB::table('product_options')
                            ->where('id', $row->product_option_id)
                            ->whereNull('product_id')
                            ->update(['product_id' => $row->product_id]);
                    }
                });

            Schema::dropIfExists('product_product_option');
        }
    }
};
