<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('memberships', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->uuid('company_id');
            $table->string('role'); // owner / admin / recruiter / interviewer / viewer
            $table->enum('status', ['active', 'revoked'])->default('active');
            $table->timestamps();

            $table->unique(['user_id', 'company_id', 'role']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('memberships');
    }
};
