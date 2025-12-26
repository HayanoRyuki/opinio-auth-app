<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('revoked_tokens', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('jti')->unique();
            $table->string('revoked_reason'); // logout / role_switch / company_switch
            $table->timestamp('revoked_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('revoked_tokens');
    }
};
