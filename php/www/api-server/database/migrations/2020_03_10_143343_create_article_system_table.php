<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateArticleSystemTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('article', function (Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->string('title', 128);
            $table->text('body')->comment('markdown格式文本');
            $table->integer('views')->default(0)->unsigned()->comment('阅读量');
            $table->integer('comments')->default(0)->unsigned()->comment('评论数');
            $table->integer('sort')->default(0)->comment('排序值')->index('idx_sort');
            $table->timestamps();
        });

        Schema::create('category', function (Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->string('name', 32);
            $table->string('key', 32)->index('idx_key');
            $table->string('color', 6);
            $table->integer('sort')->default(0)->comment('排序');
            $table->timestamps();
        });

        Schema::create('article_category', function (Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->integer('article_id')->unsigned()->index('idx_article_id');
            $table->integer('category_id')->unsigned()->index('idx_category_id');
        });

        Schema::create('article_comment', function (Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->integer('article_id')->unsigned()->index('idx_article_id');
            $table->integer('is_author')->default(0);
            $table->string('nickname', 32);
            $table->string('email', 32)->default('');
            $table->string('ip', 20)->default('');
            $table->text('content');
            $table->tinyInteger('status')->default(0);
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
        Schema::dropIfExists('article');
        Schema::dropIfExists('category');
        Schema::dropIfExists('article_category');
        Schema::dropIfExists('article_comment');
    }
}
