<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Comment;
use App\Models\Reaction;


class ReactionComment extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function comment()
    {
        return $this->belongsTo(Comment::class, "comment_id", "comment_id");
    }
    public function reaction()
    {
        return $this->belongsTo(Reaction::class, "reaction_id", "reaction_id");
    }
    
}
