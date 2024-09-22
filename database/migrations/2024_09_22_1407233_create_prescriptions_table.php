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
        Schema::create('prescription', function (Blueprint $table) {
            $table->id();
            $table->foreignId('visit_id')->constrained('visit')->onDelete('cascade');
            // Define the foreign key referencing 'num_enr' in the 'medicament' table
            $table->string('medicament_num_enr', 5);
            $table->foreign('medicament_num_enr')->references('num_enr')->on('medicament')->onDelete('cascade');            $table->string('dosage_instructions', 255);
            $table->integer('quantity');
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prescriptions');
    }
};
