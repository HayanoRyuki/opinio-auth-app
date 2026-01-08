<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('client_user', function (Blueprint $table) {
            $table->id();

            $table->char('user_id', 36);
            $table->char('client_id', 36);

            $table->string('role');

            $table->timestamps();

            $table->unique(['user_id', 'client_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('client_user');
    }
};

