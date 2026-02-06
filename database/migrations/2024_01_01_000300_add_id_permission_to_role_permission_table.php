<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('role_permission') && !Schema::hasColumn('role_permission', 'id_permission')) {
            Schema::table('role_permission', function (Blueprint $table) {
                $table->unsignedBigInteger('id_permission')->nullable()->after('id_role');
                
                $table->foreign('id_permission')
                    ->references('id_permission')
                    ->on('permission')
                    ->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasTable('role_permission') && Schema::hasColumn('role_permission', 'id_permission')) {
            Schema::table('role_permission', function (Blueprint $table) {
                $table->dropForeign(['id_permission']);
                $table->dropColumn('id_permission');
            });
        }
    }
};
