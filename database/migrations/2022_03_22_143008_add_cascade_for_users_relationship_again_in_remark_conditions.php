<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCascadeForUsersRelationshipAgainInRemarkConditions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('remark_conditions', function (Blueprint $table) {
            $table->dropForeign('remark_conditions_user_id_foreign');
            $table->dropIndex('remark_conditions_user_id_foreign');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('remark_conditions', function (Blueprint $table) {
            //
        });
    }
}
