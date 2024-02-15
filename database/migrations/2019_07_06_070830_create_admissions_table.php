<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdmissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admissions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('branch_id')->nullable();
            $table->string('org_id', '15');
            $table->string('first_name', '151');
            $table->string('admission_date', '15')->nullable();
            $table->string('admission_fee', '15')->nullable();
            $table->dateTime('date_of_birth')->nullable();
            $table->string('address', '100')->nullable();
            $table->string('country', '100')->nullable();
            $table->string('state', '100')->nullable();
            $table->string('city', '100')->nullable();
            $table->string('gender', '10')->nullable();
            $table->string('course','100')->nullable();
            $table->string('academic_status','100')->nullable();
            $table->string('email', '100')->nullable();
            $table->string('mobile', '100')->nullable();
            $table->string('extra_info', '100')->nullable();
            $table->string('responce', '100')->nullable();
            $table->string('reference', '100')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('admissions');
    }
}
