<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUnbouncesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('unbounces', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('lead_id');

            // custom info
            $table->string('name')->nullable();
            $table->string('telephone')->nullable();
            $table->string('email')->nullable();
            $table->string('division')->nullable();
            $table->string('club')->nullable();
            $table->string('owner')->nullable();
            $table->string('salesperson')->nullable();
            $table->longText('notes')->nullable();

            // unbounce info
            $table->string('ip_address')->nullable();
            $table->string('page_uuid')->nullable();
            $table->string('variant')->nullable();
            $table->string('time_submitted')->nullable();
            $table->string('date_submitted')->nullable();
            $table->string('page_url')->nullable();
            $table->string('page_name')->nullable();
            $table->string('spouse')->nullable();

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
        Schema::dropIfExists('unbounces');
    }
}
