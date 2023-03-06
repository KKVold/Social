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
        Schema::create('reaction_posts', function (Blueprint $table) {
            $table->foreignId("reaction_id")->constrained("reactions", "reaction_id")->onDelete("cascade");
            $table->foreignId("post_id")->constrained("posts", "post_id")->onDelete("cascade");
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
        Schema::dropIfExists('reaction_posts');
    }
};
