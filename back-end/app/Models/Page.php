<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\TagPage;
use App\Models\User;
use App\Models\Post;
use App\Models\PageUser;

class Page extends Model
{
    use HasFactory;
    protected $guarded = [
        'page_id'
    ];
    protected $primaryKey = "page_id";
    public function owner()
    {
        return $this->belongsTo(User::class, "user_id", "user_id");
    }
    public function posts()
    {
        return $this->hasMany(Post::class, "page_id", "page_id");
    }
    public function page_users()
    {
        return $this->hasMany(PageUser::class, "page_id", "page_id");
    }
    public function page_tags()
    {
        return $this->hasMany(TagPage::class, "page_id", "page_id");
    }
}
