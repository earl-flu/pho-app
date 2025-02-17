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
        Schema::create('pr_signatories', function (Blueprint $table) {
            $table->id();
            $table->string('requested_by');
            $table->string('requested_by_position');
            $table->string('cash_availability');
            $table->string('cash_availability_position');
            $table->string('approved_by');
            $table->string('approved_by_position');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pr_signatories');
    }
};
