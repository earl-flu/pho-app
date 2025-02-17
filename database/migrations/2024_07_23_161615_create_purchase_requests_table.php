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
        Schema::create('purchase_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('budget_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->text('remarks')->nullable();
            $table->text('number')->nullable();
            $table->string('status')->default('pending');
            $table->dateTime('date_approved', precision: 0)->nullable(); // required if status is approved
            $table->boolean('is_received')->default(0);
            $table->dateTime('date_received', precision: 0)->nullable(); //required if received is true in validation
            
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
        Schema::dropIfExists('purchase_requests');
    }
};
