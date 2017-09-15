<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAddsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('adds', function (Blueprint $table) {
            $table->increments('id');
            $table->string('club_id');
            $table->string('member_number')->nullable();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('spouse_first_name')->nullable();
            $table->string('spouse_last_name')->nullable();
            $table->string('email')->nullable();
            $table->string('spouse_email')->nullable();
            $table->string('phone')->nullable();
            $table->string('address_1')->nullable();
            $table->string('address_2')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('zip')->nullable();
            $table->string('member_type')->nullable();
            $table->float('monthly_dues')->nullable();
            $table->date('marketed_at')->nullable();
            $table->date('joined_at')->nullable();
            $table->float('initiation_fee')->nullable();
            $table->string('channel')->nullable();
            $table->string('membership_interest')->nullable();
            $table->string('form_type')->nullable();
            $table->longText('matched_on')->nullable();
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
        Schema::dropIfExists('adds');
    }
}
