<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('products') || ! Schema::hasTable('translations')) {
            return;
        }

        if (! Schema::hasColumn('products', 'meta_title_id')) {
            Schema::table('products', function (Blueprint $table) {
                $table->foreignId('meta_title_id')
                    ->nullable()
                    ->after('weight')
                    ->constrained('translations')
                    ->nullOnDelete();
            });
        }

        if (! Schema::hasColumn('products', 'meta_description_id')) {
            Schema::table('products', function (Blueprint $table) {
                $table->foreignId('meta_description_id')
                    ->nullable()
                    ->after('meta_title_id')
                    ->constrained('translations')
                    ->nullOnDelete();
            });
        }

        $now = now();
        $select = ['id', 'meta_title_id', 'meta_description_id'];
        if (Schema::hasColumn('products', 'meta_title')) {
            $select[] = 'meta_title';
        }
        if (Schema::hasColumn('products', 'meta_description')) {
            $select[] = 'meta_description';
        }

        $rows = DB::table('products')->select($select)->get();

        foreach ($rows as $row) {
            $titleId = $row->meta_title_id;
            $descriptionId = $row->meta_description_id;

            if (empty($titleId) && Schema::hasColumn('products', 'meta_title') && filled($row->meta_title ?? null)) {
                $titleId = DB::table('translations')->insertGetId([
                    'english' => $row->meta_title,
                    'romanian' => null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }

            if (empty($descriptionId) && Schema::hasColumn('products', 'meta_description') && filled($row->meta_description ?? null)) {
                $descriptionId = DB::table('translations')->insertGetId([
                    'english' => $row->meta_description,
                    'romanian' => null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }

            DB::table('products')->where('id', $row->id)->update([
                'meta_title_id' => $titleId,
                'meta_description_id' => $descriptionId,
            ]);
        }

        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'meta_title')) {
                $table->dropColumn('meta_title');
            }
            if (Schema::hasColumn('products', 'meta_description')) {
                $table->dropColumn('meta_description');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('products')) {
            return;
        }

        Schema::table('products', function (Blueprint $table) {
            if (! Schema::hasColumn('products', 'meta_title')) {
                $table->string('meta_title')->nullable();
            }
            if (! Schema::hasColumn('products', 'meta_description')) {
                $table->string('meta_description', 512)->nullable();
            }
        });

        $rows = DB::table('products')->select('id', 'meta_title_id', 'meta_description_id')->get();
        foreach ($rows as $row) {
            $title = null;
            $description = null;
            if (! empty($row->meta_title_id)) {
                $title = DB::table('translations')->where('id', $row->meta_title_id)->value('english');
            }
            if (! empty($row->meta_description_id)) {
                $description = DB::table('translations')->where('id', $row->meta_description_id)->value('english');
            }
            DB::table('products')->where('id', $row->id)->update([
                'meta_title' => $title,
                'meta_description' => $description,
            ]);
        }

        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'meta_title_id')) {
                $table->dropForeign(['meta_title_id']);
                $table->dropColumn('meta_title_id');
            }
            if (Schema::hasColumn('products', 'meta_description_id')) {
                $table->dropForeign(['meta_description_id']);
                $table->dropColumn('meta_description_id');
            }
        });
    }
};
