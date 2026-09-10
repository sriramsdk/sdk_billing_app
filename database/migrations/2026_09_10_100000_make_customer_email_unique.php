<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $duplicateEmails = DB::table('customers')
            ->select('email')
            ->whereNotNull('email')
            ->where('email', '<>', '')
            ->groupBy('email')
            ->havingRaw('COUNT(*) > 1')
            ->pluck('email');

        foreach ($duplicateEmails as $email) {
            $customerIds = DB::table('customers')
                ->where('email', $email)
                ->orderBy('id')
                ->pluck('id');
            $keeperId = $customerIds->first();

            DB::table('orders')
                ->whereIn('customer_id', $customerIds->skip(1))
                ->update(['customer_id' => $keeperId]);

            DB::table('customers')
                ->whereIn('id', $customerIds->skip(1))
                ->delete();
        }

        Schema::table('customers', function (Blueprint $table) {
            $table->unique('email', 'customers_email_unique');
        });
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropUnique('customers_email_unique');
        });
    }
};