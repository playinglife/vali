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
        if (! Schema::hasTable('products') || ! Schema::hasTable('translations')) {
            return;
        }

        if (! Schema::hasColumn('products', 'short_description_id')) {
            Schema::table('products', function (Blueprint $table) {
                $table->foreignId('short_description_id')
                    ->nullable()
                    ->constrained('translations')
                    ->nullOnDelete();
            });
        }

        if (! Schema::hasColumn('products', 'description_id')) {
            Schema::table('products', function (Blueprint $table) {
                $table->foreignId('description_id')
                    ->nullable()
                    ->constrained('translations')
                    ->nullOnDelete();
            });
        }

        if (! Schema::hasColumn('products', 'short_description') && ! Schema::hasColumn('products', 'description')) {
            return;
        }

        $now = now();
        $rows = DB::table('products')->select('*')->get();

        foreach ($rows as $row) {
            $shortId = null;
            $longId = null;

            if (Schema::hasColumn('products', 'short_description') && $row->short_description !== null && $row->short_description !== '') {
                $shortId = DB::table('translations')->insertGetId([
                    'english' => $row->short_description,
                    'romanian' => null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }

            if (Schema::hasColumn('products', 'description') && $row->description !== null && $row->description !== '') {
                $longId = DB::table('translations')->insertGetId([
                    'english' => $row->description,
                    'romanian' => null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }

            DB::table('products')->where('id', $row->id)->update([
                'short_description_id' => $shortId,
                'description_id' => $longId,
            ]);
        }

        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'short_description')) {
                $table->dropColumn('short_description');
            }
            if (Schema::hasColumn('products', 'description')) {
                $table->dropColumn('description');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('products')) {
            return;
        }

        Schema::table('products', function (Blueprint $table) {
            if (! Schema::hasColumn('products', 'short_description')) {
                $table->string('short_description')->nullable();
            }
            if (! Schema::hasColumn('products', 'description')) {
                $table->text('description')->nullable();
            }
        });

        if (Schema::hasColumn('products', 'short_description_id') || Schema::hasColumn('products', 'description_id')) {
            $rows = DB::table('products')->select('*')->get();
            foreach ($rows as $row) {
                $short = null;
                $long = null;
                if (! empty($row->short_description_id)) {
                    $short = DB::table('translations')->where('id', $row->short_description_id)->value('english');
                }
                if (! empty($row->description_id)) {
                    $long = DB::table('translations')->where('id', $row->description_id)->value('english');
                }
                DB::table('products')->where('id', $row->id)->update([
                    'short_description' => $short,
                    'description' => $long,
                ]);
            }
        }

        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'short_description_id')) {
                $table->dropForeign(['short_description_id']);
                $table->dropColumn('short_description_id');
            }
            if (Schema::hasColumn('products', 'description_id')) {
                $table->dropForeign(['description_id']);
                $table->dropColumn('description_id');
            }
        });
    }
};
