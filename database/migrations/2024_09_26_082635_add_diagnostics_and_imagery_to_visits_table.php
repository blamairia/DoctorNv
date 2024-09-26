<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDiagnosticsAndImageryToVisitsTable extends Migration
{
    public function up()
    {
        Schema::table('visit', function (Blueprint $table) {
            $table->text('blood_work_diagnostics')->nullable();
            $table->text('mri_scans')->nullable();
            $table->text('xray_scans')->nullable();
        });
    }

    public function down()
    {
        Schema::table('visit', function (Blueprint $table) {
            $table->dropColumn(['blood_work_diagnostics', 'mri_scans', 'xray_scans']);
        });
    }
}
