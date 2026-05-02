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
        Schema::table('mentor_profiles', function (Blueprint $table) {
            $table->boolean('is_approved')->default(false)->after('cover_photo');
        });

        // Approve all existing mentor profiles so current mentors are not hidden
        DB::table('mentor_profiles')->update(['is_approved' => true]);
    }

    public function down(): void
    {
        Schema::table('mentor_profiles', function (Blueprint $table) {
            $table->dropColumn('is_approved');
        });
    }
};
