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
        Schema::create('users', function (Blueprint $table) {
            $table->id("user_id");
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique();
            //$table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            //$table->rememberToken();
            $table->date("birth_date");
            $table->string("profile_photo")->default("upload/photos/profile_photos/default_profile_photo.png");
            $table->string("gender");
            $table->string("phone_number");
            $table->string("country")->nullable();
            $table->string("city")->nullable();
            $table->text("about_me")->nullable();
            $table->dateTime("last_seen")->nullable();
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
        Schema::dropIfExists('users');
    }
};
