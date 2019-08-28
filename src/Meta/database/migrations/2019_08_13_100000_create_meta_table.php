<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMetaTable extends Migration
{
    public function up()
    {
        Schema::create('meta', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('metable_id')->index();
            $table->string('metable_type');
            $table->string('title', 100)->nullable();
            $table->string('description', 200)->nullable();
            $table->string('og_title', 100)->nullable();
            $table->string('og_description', 200)->nullable();
            $table->text('custom_tags')->nullable();
            $table->timestamps();
        });
    }
    public function down()
    {
        Schema::dropIfExists('meta');
    }
}
