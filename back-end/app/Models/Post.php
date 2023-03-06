<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\TagPost;
use App\Models\Media;
use App\Models\Comment;
use App\Models\Page;
use App\Models\ReactionPost;
use App\Models\Group;

class Post extends Model
{
    use HasFactory;
    protected $guarded = [
        'post_id'
    ];
    protected $primaryKey = "post_id";
    public function auther()
    {
        return $this->belongsTo(User::class, "user_id", "user_id");
    }
    public function tag_posts()
    {
        return $this->hasMany(TagPost::class, "post_id", "post_id");
    }
    public function media()
    {
        return $this->hasMany(Media::class, "post_id", "post_id");
    }
    public function comments()
    {
        return $this->hasMany(Comment::class, "post_id", "post_id");
    }
    public function page()
    {
        return $this->belongsTo(Page::class, "page_id", "page_id");
    }
    public function post_reactions()
    {
        return $this->hasMany(ReactionPost::class, "post_id", "post_id");
    }
    public function group()
    {
        return $this->belongsTo(Group::class, "group_id", "group_id");
    }
}
