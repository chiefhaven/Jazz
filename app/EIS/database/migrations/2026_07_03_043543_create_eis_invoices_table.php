<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('eis_invoices', function (Blueprint $table) {
            $table->id();

            $table->string('invoice_number')->unique();
            $table->string('tin')->nullable();
            $table->string('customer_name')->nullable();

            $table->json('payload');

            $table->string('status')->default('pending');
            // pending, submitted, failed

            $table->integer('attempts')->default(0);
            $table->text('last_error')->nullable();

            $table->timestamp('submitted_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('eis_invoices');
    }
};