<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMediaTable extends Migration
{
    public function up()
    {
        Schema::create('media_folders', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('parent_id')->index()->nullable();
            $table->string('name');
            $table->timestamps();

            $table->foreign('parent_id')
                  ->references('id')
                  ->on('media_folders')
                  ->onDelete('cascade');
        });

        Schema::create('media', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('folder_id')->index()->nullable();
            $table->string('name');
            $table->string('alt_text')->nullable();
            $table->string('caption')->nullable();
            $table->string('file_name');
            $table->string('disk');
            $table->string('mime_type');
            $table->unsignedInteger('size');
            $table->timestamps();

            $table->foreign('folder_id')
                  ->references('id')
                  ->on('media_folders')
                  ->onDelete('cascade');
        });

        Schema::create('mediables', function (Blueprint $table) {
            $table->unsignedInteger('media_id')->index();
            $table->unsignedInteger('mediable_id')->index();
            $table->string('mediable_type');
            $table->string('group');

            $table->foreign('media_id')
                  ->references('id')
                  ->on('media')
                  ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('mediables');
        Schema::dropIfExists('media');
        Schema::dropIfExists('media_folders');
    }
}
