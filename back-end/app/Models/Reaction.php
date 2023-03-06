<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ReactionComment;
use App\Models\ReactionPost;

class Reaction extends Model
{
    use HasFactory;
    protected $guarded = [
        'reaction_id'
    ];
    protected $primaryKey = "reaction_id";
    public function reaction_comments()
    {
        return $this->hasMany(ReactionComment::class, "reaction_id", "reaction_id");
    }
    public function reaction_posts()
    {
        return $this->hasMany(ReactionPost::class, "reaction_id", "reaction_id");
    }
}
