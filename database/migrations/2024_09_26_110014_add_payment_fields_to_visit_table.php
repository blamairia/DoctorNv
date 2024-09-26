<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPaymentFieldsToVisitTable extends Migration
{
    public function up()
    {
        Schema::table('visit', function (Blueprint $table) {
            $table->decimal('payment_total', 10, 2)->nullable(); // Total payment
            $table->string('payment_status')->nullable();        // Payment status
            $table->decimal('debt', 10, 2)->default(0);         // Debt amount
        });
    }

    public function down()
    {
        Schema::table('visit', function (Blueprint $table) {
            $table->dropColumn('payment_total');
            $table->dropColumn('payment_status');
            $table->dropColumn('debt');
        });
    }
}
