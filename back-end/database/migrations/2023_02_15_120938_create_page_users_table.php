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
        Schema::create('page_users', function (Blueprint $table) {
            $table->foreignId("user_id")->constrained("users", "user_id")->onDelete("cascade");
            $table->foreignId("page_id")->constrained("pages", "page_id")->onDelete("cascade");
            $table->foreignId("role_id")->constrained("roles", "role_id")->onDelete("cascade");
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
        Schema::dropIfExists('page_users');
    }
};
