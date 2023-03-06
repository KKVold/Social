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
        Schema::create('friends', function (Blueprint $table) {
            $table->foreignId("first_user_id")->constrained("users", "user_id")->onDelete("cascade");
            $table->foreignId("second_user_id")->constrained("users", "user_id")->onDelete("cascade");
            $table->unsignedInteger("status");
            /*
            status:
                first and second bit for block.
                    the first bit for first user
                    the second bit for second user
                third bit for waiting request friend from first user to the second user
            */
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
        Schema::dropIfExists('friends');
    }
};
