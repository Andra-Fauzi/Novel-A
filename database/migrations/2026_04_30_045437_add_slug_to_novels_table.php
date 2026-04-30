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
        Schema::table('novels', function (Blueprint $table) {
            $table->string('slug')->nullable()->after('title');
        });

        // Backfill existing novels
        $novels = \DB::table('novels')->get();
        foreach ($novels as $novel) {
            $slug = \Str::slug($novel->title) . '-' . $novel->id;
            \DB::table('novels')->where('id', $novel->id)->update(['slug' => $slug]);
        }

        Schema::table('novels', function (Blueprint $table) {
            $table->unique('slug');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('novels', function (Blueprint $table) {
            $table->dropColumn('slug');
        });
    }
};
