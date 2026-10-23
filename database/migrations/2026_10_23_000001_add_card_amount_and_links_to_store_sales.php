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
        Schema::table('store_sales', function (Blueprint $table) {
            $table->decimal('card_amount', 10, 2)->default(0)->after('qr_amount');
            $table->unsignedBigInteger('qr_payment_id')->nullable()->after('card_amount');
            $table->unsignedBigInteger('invoice_id')->nullable()->after('qr_payment_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('store_sales', function (Blueprint $table) {
            $table->dropColumn(['card_amount', 'qr_payment_id', 'invoice_id']);
        });
    }
};
