<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdatePrItemDetailsTable extends Migration
{
     /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('pr_item_details', function (Blueprint $table) {
            $table->text('description')->change(); // Change description to text
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pr_item_details', function (Blueprint $table) {
            $table->string('description')->change(); // Revert back to string
        });
    }
};
