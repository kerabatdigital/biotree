<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            // Our canonical local reference (billExternalReferenceNo / callback order_id).
            // Set at payment creation, before the gateway bill exists.
            $table->string('external_ref')->nullable()->unique()->after('user_id');

            // The gateway bill code (ToyyibPay BillCode) is only known *after* the bill
            // is created, so it must be nullable.
            $table->string('bill_code')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropUnique(['external_ref']);
            $table->dropColumn('external_ref');
            $table->string('bill_code')->nullable(false)->change();
        });
    }
};
