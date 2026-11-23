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
        Schema::create('tax_invoices', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('tax_invoice_number')->unique()->comment('Faktur Pajak Serial e.g. 010.000-26.00000001');
            $table->foreignId('invoice_header_id')->constrained('invoice_headers')->cascadeOnDelete();
            $table->string('tax_period', 7)->comment('MM-YYYY e.g. 07-2026');
            $table->decimal('dpp_amount', 15, 2)->comment('Dasar Pengenaan Pajak');
            $table->decimal('ppn_amount', 15, 2)->comment('Pajak Pertambahan Nilai (PPN)');
            $table->enum('status', ['draft', 'approved', 'reported', 'cancelled'])->default('draft');
            $table->string('qr_code_url')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['tax_invoice_number', 'tax_period', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tax_invoices');
    }
};
