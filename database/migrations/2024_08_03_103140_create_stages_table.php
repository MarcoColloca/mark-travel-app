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
        Schema::create('stages', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('slug', 105)->unique();
            $table->text('cover')->nullable();
            $table->text('notes')->nullable();
            $table->text('description')->nullable();
            $table->text('curiosities')->nullable();
            $table->TinyInteger('rating')->default(-1);
            $table->string('address');
            $table->float('lat', 12,9)->nullable();
            $table->float('lon', 12,9)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stages');
    }
};
