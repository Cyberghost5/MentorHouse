<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('session_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mentee_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('mentor_id')->constrained('users')->cascadeOnDelete();
            $table->enum('status', ['pending', 'accepted', 'declined', 'completed', 'cancelled'])->default('pending');
            $table->enum('session_type', ['free', 'paid', 'project_based']);
            $table->text('message')->nullable();
            $table->dateTime('proposed_date');
            $table->decimal('fee_amount', 8, 2)->nullable();
            $table->text('project_description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('session_requests');
    }
};
