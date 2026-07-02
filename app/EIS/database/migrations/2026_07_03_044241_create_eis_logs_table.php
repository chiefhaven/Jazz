<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('eis_logs', function (Blueprint $table) {
            $table->id();

            $table->string('type'); 
            // invoice, auth, stock, utility, onboarding

            $table->string('action'); 
            // submit, retry, failed, success, activate

            $table->string('reference')->nullable(); 
            // invoice number, terminal id, etc.

            $table->longText('request_payload')->nullable();
            $table->longText('response_payload')->nullable();

            $table->string('status')->default('info');
            // info, success, error

            $table->text('message')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('eis_logs');
    }
};