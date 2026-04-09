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
        if (! Schema::hasTable('product_product_option_value')) {
            Schema::create('product_product_option_value', function (Blueprint $table) {
                $table->id();
                $table->foreignId('product_id')->constrained()->cascadeOnDelete();
                $table->foreignId('product_option_value_id')->constrained()->cascadeOnDelete();
                $table->timestamps();

                $table->unique(['product_id', 'product_option_value_id'], 'ppov_product_value_unique');
                $table->index(['product_id', 'product_option_value_id'], 'ppov_product_value_idx');
            });
        }

        if (Schema::hasTable('product_product_option')) {
            DB::table('product_product_option as ppo')
                ->join('product_option_values as pov', 'pov.product_option_id', '=', 'ppo.product_option_id')
                ->select([
                    'ppo.product_id as product_id',
                    'pov.id as product_option_value_id',
                    'ppo.created_at as created_at',
                    'ppo.updated_at as updated_at',
                ])
                ->orderBy('ppo.id')
                ->chunk(1000, function ($rows) {
                    $payload = [];
                    foreach ($rows as $row) {
                        $payload[] = [
                            'product_id' => (int) $row->product_id,
                            'product_option_value_id' => (int) $row->product_option_value_id,
                            'created_at' => $row->created_at,
                            'updated_at' => $row->updated_at,
                        ];
                    }

                    if ($payload !== []) {
                        DB::table('product_product_option_value')->upsert(
                            $payload,
                            ['product_id', 'product_option_value_id'],
                            ['updated_at']
                        );
                    }
                });
        } elseif (Schema::hasColumn('product_options', 'product_id')) {
            DB::table('product_options as po')
                ->join('product_option_values as pov', 'pov.product_option_id', '=', 'po.id')
                ->whereNotNull('po.product_id')
                ->select([
                    'po.product_id as product_id',
                    'pov.id as product_option_value_id',
                    'pov.created_at as created_at',
                    'pov.updated_at as updated_at',
                ])
                ->orderBy('po.id')
                ->chunk(1000, function ($rows) {
                    $payload = [];
                    foreach ($rows as $row) {
                        $payload[] = [
                            'product_id' => (int) $row->product_id,
                            'product_option_value_id' => (int) $row->product_option_value_id,
                            'created_at' => $row->created_at,
                            'updated_at' => $row->updated_at,
                        ];
                    }

                    if ($payload !== []) {
                        DB::table('product_product_option_value')->upsert(
                            $payload,
                            ['product_id', 'product_option_value_id'],
                            ['updated_at']
                        );
                    }
                });
        }

        Schema::dropIfExists('product_product_option');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('product_product_option')) {
            Schema::create('product_product_option', function (Blueprint $table) {
                $table->id();
                $table->foreignId('product_id')->constrained()->cascadeOnDelete();
                $table->foreignId('product_option_id')->constrained()->cascadeOnDelete();
                $table->unsignedInteger('sort_order')->default(0);
                $table->timestamps();

                $table->unique(['product_id', 'product_option_id'], 'ppo_product_option_unique');
            });
        }

        if (Schema::hasTable('product_product_option_value')) {
            DB::table('product_product_option_value as ppov')
                ->join('product_option_values as pov', 'pov.id', '=', 'ppov.product_option_value_id')
                ->select([
                    'ppov.product_id as product_id',
                    'pov.product_option_id as product_option_id',
                    'ppov.created_at as created_at',
                    'ppov.updated_at as updated_at',
                ])
                ->orderBy('ppov.id')
                ->chunk(1000, function ($rows) {
                    $payload = [];
                    foreach ($rows as $row) {
                        $payload[] = [
                            'product_id' => (int) $row->product_id,
                            'product_option_id' => (int) $row->product_option_id,
                            'sort_order' => 0,
                            'created_at' => $row->created_at,
                            'updated_at' => $row->updated_at,
                        ];
                    }

                    if ($payload !== []) {
                        DB::table('product_product_option')->upsert(
                            $payload,
                            ['product_id', 'product_option_id'],
                            ['updated_at']
                        );
                    }
                });

            Schema::dropIfExists('product_product_option_value');
        }
    }
};
