<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBranchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('branches', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('org_id')->nullable();
            $table->string('branch_name', '151');
            $table->string('branch_title', '151');
            $table->string('branch_email', '151')->nullable();
            $table->string('branch_mobile', '115')->nullable();
            $table->string('branch_logo', '151')->nullable();
            $table->string('branch_descriptiojn', '512')->nullable();
            $table->string('branch_manager', '151')->nullable();
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
        Schema::dropIfExists('branches');
    }
}
