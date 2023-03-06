<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\User;
use App\Models\Post;
use App\Models\ReactionComment;

class Comment extends Model
{
    use HasFactory;
    protected $primaryKey = "comment_id";
    protected $guarded = [
        'comment_id'
    ];
    public function parent()
    {
        return $this->belongsTo(Comment::class, "parent_comment_id", "comment_id");
    }
    public function auther()
    {
        return $this->belongsTo(User::class, "user_id", "user_id");
    }
    public function post()
    {
        return $this->belongsTo(Post::class, "post_id", "post_id");
    }
    public function comment_reactions()
    {
        return $this->hasMany(ReactionComment::class, "comment_id", "comment_id");
    }
}
