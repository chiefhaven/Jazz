<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTaxOfficeNameToEisConfigurations extends Migration
{
    public function up()
    {
        Schema::table('eis_configurations', function (Blueprint $table) {
            $table->string('tax_office_name')->nullable()->after('tax_office_code');
        });
    }

    public function down()
    {
        Schema::table('eis_configurations', function (Blueprint $table) {
            $table->dropColumn('tax_office_name');
        });
    }
}