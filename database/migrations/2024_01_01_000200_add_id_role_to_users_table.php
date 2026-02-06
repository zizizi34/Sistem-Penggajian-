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
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'id_role')) {
                $table->unsignedBigInteger('id_role')->nullable()->after('password');
                
                // Foreign key reference
                $table->foreign('id_role')
                    ->references('id_role')
                    ->on('role')
                    ->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'id_role')) {
                $table->dropForeign(['id_role']);
                $table->dropColumn('id_role');
            }
        });
    }
};
