<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Post;

class Media extends Model
{
    use HasFactory;
    protected $guarded = [
        'media_id'
    ];
    protected $primaryKey = "media_id";
    public function post()
    {
        return $this->belongsTo(Post::class, "post_id", "post_id");
    }
}
