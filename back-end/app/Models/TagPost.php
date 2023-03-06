<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Post;
use App\Models\Tag;

class TagPost extends Model
{
    use HasFactory;
    protected $guarded = [];
    public function post()
    {
        return $this->belongsTo(Post::class, "post_id", "post_id");
    }
    public function tag()
    {
        return $this->belongsTo(Tag::class, "tag_id", "tag_id");
    }
}
