<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('ice_candidates', function (Blueprint $table) {
            $table->id();
            $table->string('room_id', 100);
            $table->unsignedBigInteger('user_id');
            $table->longText('candidate_json');
            $table->timestamps();

            $table->index(['room_id', 'user_id']);
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ice_candidates');
    }
};
