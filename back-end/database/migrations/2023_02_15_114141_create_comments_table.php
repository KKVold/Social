<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('comments', function (Blueprint $table) {
            $table->id("comment_id");
            $table->foreignId("user_id")->constrained("users", "user_id")->onDelete("cascade");
            $table->foreignId("post_id")->constrained("posts", "post_id")->onDelete("cascade");
            $table->foreignId("parent_comment_id")->nullable()->constrained("comments", "comment_id")->onDelete("cascade");
            $table->unsignedInteger("level")->default(1);
            $table->longText("content")->nullable();
            $table->string("media_path")->nullable();
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
        Schema::dropIfExists('comments');
    }
};
