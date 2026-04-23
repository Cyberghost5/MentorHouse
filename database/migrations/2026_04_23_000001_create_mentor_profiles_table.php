<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mentor_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->json('expertise')->nullable();
            $table->enum('availability', ['open', 'closed'])->default('open');
            $table->enum('session_type', ['free', 'paid', 'project_based'])->default('free');
            $table->decimal('hourly_rate', 8, 2)->nullable();
            $table->unsignedSmallInteger('years_of_experience')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mentor_profiles');
    }
};
