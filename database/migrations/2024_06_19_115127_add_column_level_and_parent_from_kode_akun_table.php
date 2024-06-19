<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnLevelAndParentFromKodeAkunTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('kode_akun', function (Blueprint $table) {
            $table->enum('level', [1, 2, 3, 4])->nullable()->after('saldo_awal');
            $table->integer('parent')->nullable()->after('level');
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
            $table->dropColumn('level');
            $table->dropColumn('parent');
        });
    }
}
