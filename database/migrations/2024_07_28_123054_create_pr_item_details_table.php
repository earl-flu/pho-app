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
        Schema::create('pr_item_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')
                ->constrained('pr_items')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->string('description');
            $table->string('uom'); // Unit of Measurement
            $table->string('website_link')->nullable();
            $table->decimal('original_price', 15, 2);
            $table->decimal('markup_price', 15, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pr_item_details');
    }
};
