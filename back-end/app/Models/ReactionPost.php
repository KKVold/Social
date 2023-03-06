<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Reaction;
use App\Models\Post;


class ReactionPost extends Model
{
    use HasFactory;
    protected $guarded = [];
    public function reaction()
    {
        return $this->belongsTo(Reaction::class, "reaction_id", "reaction_id");
    }
    public function post()
    {
        return $this->belongsTo(Post::class, "post_id", "post_id");
    }
    
}
