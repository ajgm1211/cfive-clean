<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQuotaRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quota_requests', function (Blueprint $table) {
            Schema::create('quota_requests', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('quota')->default(0);
                $table->enum('type',['limited','unlimited']);
                $table->enum('payment_type',['monthly','biannual','annual'])->nullable();
                $table->date('issued_date');
            $table->date('due_date')->nullable();
                $table->boolean('status')->default(1);
                $table->integer('company_user_id')->unsigned();
                $table->foreign('company_user_id')->references('id')->on('company_users');
                $table->timestamps();
            });
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('quota_requests');
    }
}
