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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('customer_code')->unique();
            $table->string('name');
            $table->string('company_name');
            $table->string('email')->unique();
            $table->string('phone', 30)->nullable();
            $table->string('npwp', 35)->nullable()->comment('Nomor Pokok Wajib Pajak');
            $table->string('nik', 20)->nullable()->comment('Nomor Induk Kependudukan');
            $table->text('address');
            $table->text('billing_address')->nullable();
            $table->string('city', 100)->default('Bandar Lampung');
            $table->string('postal_code', 10)->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['customer_code', 'company_name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
