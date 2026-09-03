<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('peminjaman', function (Blueprint $table) {
            $table->date('tgl_kembali_real')->nullable()->after('tgl_kembali_plan');
            $table->decimal('denda', 12, 2)->default(0)->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('peminjaman', function (Blueprint $table) {
            $table->dropColumn(['tgl_kembali_real', 'denda']);
        });
    }
};