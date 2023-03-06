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
        Schema::disableForeignKeyConstraints();
        Schema::create('posts', function (Blueprint $table) {
            $table->id("post_id");
            $table->foreignId("user_id")->constrained("users", "user_id")->onDelete("cascade");
            $table->foreignId("page_id")->nullable()->constrained("pages", "page_id")->onDelete("cascade");
            $table->foreignId("group_id")->nullable()->constrained("groups", "group_id")->onDelete("cascade");
            $table->longText("content")->nullable();
            $table->timestamps();
        });
        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('posts');
    }
};
