<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBillingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('billings', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code');
            $table->integer('patient_id');
            $table->integer('doctor_id');
            $table->integer('medical_history_id');
            $table->integer('consultation_fee');
            $table->integer('treatment_fee');
            $table->integer('medicine_fee');
            $table->integer('total');
            $table->integer('status');
            $table->timestamps();
        });

        Schema::create('billing_medicines', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('billing_id');
            $table->integer('medicine_id');
            $table->integer('quantity');
            $table->integer('total');
            $table->timestamps();
        });

        Schema::create('billing_treatments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('billing_id');
            $table->integer('treatment_id');
            $table->integer('total');
            $table->timestamps();
        });

        Schema::create('medicines', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('slug');
            $table->string('description')->nullable();
            $table->integer('price');
            $table->integer('status');
            $table->timestamps();
        });

        Schema::create('medicine_stock', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('medicine_id');
            $table->integer('available');
            $table->integer('sold');
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
        Schema::dropIfExists('billings');
        Schema::dropIfExists('billing_medicines');
        Schema::dropIfExists('medicines');
        Schema::dropIfExists('medicine_stock');
    }
}
