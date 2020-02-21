<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pages', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title');
            $table->string('slug')->nullable();
            $table->string('path')->nullable();
            $table->boolean('has_fixed_path')->default(false);
            $table->unsignedBigInteger('parent_id')->index()->nullable();
            $table->string('template_name');
            $table->boolean('has_fixed_template')->default(false);
            $table->boolean('is_standalone');
            $table->boolean('is_deletable')->default(true);
            $table->unsignedBigInteger('order');
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });

        Schema::create('page_contents', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('page_id')->index();
            $table->string('key');
            $table->text('value')->nullable();
            $table->timestamps();

            $table->foreign('page_id')
                  ->references('id')
                  ->on('pages')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('page_contents');
        Schema::dropIfExists('pages');
    }
}
