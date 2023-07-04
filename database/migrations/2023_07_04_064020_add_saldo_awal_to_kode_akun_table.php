<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSaldoAwalToKodeAkunTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('kode_akun', function (Blueprint $table) {
            $table->decimal('saldo_awal', 13, 2);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('kode_akun', function (Blueprint $table) {
            $table->dropColumn('saldo_awal');
        });
    }
}
