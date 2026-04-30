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
        Schema::create('novels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('author_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->string('cover');
            $table->text('description');
            $table->string('status')->default("Ongoing");
            $table->integer('stars')->default(0);
            $table->integer('readed')->default(0);
            $table->integer('published_year')->default(now()->year);
            $table->integer('downloaded')->default(0);
            $table->integer('volumes_total')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('novels');
    }
};
