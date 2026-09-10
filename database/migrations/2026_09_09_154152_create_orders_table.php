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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();

            $table->string('invoice_number')->unique();

            $table->foreignId('employee_id')
                ->constrained()
                ->restrictOnDelete();

            $table->foreignId('customer_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->decimal('subtotal', 12, 2);
            $table->decimal('tax_amount', 12, 2)->default(0);
            $table->decimal('grand_total', 12, 2);

            $table->decimal('amount_paid', 12, 2)->default(0);
            $table->decimal('balance_amount', 12, 2)->default(0);

            $table->enum('payment_status', [
                'pending',
                'paid',
                'partial'
            ])->default('pending');

            $table->enum('status', [
                'draft',
                'completed',
                'cancelled'
            ])->default('completed');

            $table->string('pdf_path')->nullable();

            $table->timestamp('billed_at')->nullable();

            $table->timestamps();

            $table->index(['employee_id', 'created_at']);
            $table->index(['customer_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
