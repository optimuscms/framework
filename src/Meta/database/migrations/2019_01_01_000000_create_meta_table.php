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
            $table->string('title')->nullable();
            $table->string('description')->nullable();
            $table->string('og_title')->nullable();
            $table->string('og_description')->nullable();
            $table->text('additional_tags')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('meta');
    }
}
