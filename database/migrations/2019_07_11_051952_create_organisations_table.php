<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrganisationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('organisations', function (Blueprint $table) {
            $table->increments('id');
            $table->string('organisation_name', '151');
            $table->string('organisation_title', '151');
            $table->string('organisation_email', '151')->nullable();
            $table->string('organisation_mobile', '115')->nullable();
            $table->string('organisation_logo', '151')->nullable();
            $table->string('organisation_descriptiojn', '151')->nullable();
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
        Schema::dropIfExists('organisations');
    }
}
