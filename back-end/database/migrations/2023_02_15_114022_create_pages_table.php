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
        Schema::create('pages', function (Blueprint $table) {
            $table->id("page_id");
            $table->foreignId("user_id")->constrained("users", "user_id")->onDelete("cascade");
            $table->string("name");
            $table->text("discreption")->nullable();
            $table->string("cover_path")->default("upload/photos/cover_photos/default_cover_photo.png");
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
        Schema::dropIfExists('pages');
    }
};
