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
      
        Schema::create('certificates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->uuid('certificate_id')->unique();
            $table->string('file_name');
            $table->string('sha256_hash')->unique();
            $table->timestamp('timestamp');
            $table->string('blockchain_tx')->nullable();
            $table->string('project_name')->nullable();
            $table->text('description')->nullable();
            $table->string('language')->default('en');
            $table->string('preview_url')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('certificates');
    }
};
