<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\TagGroup;
use App\Models\TagPage;
use App\Models\TagPost;
use App\Models\TagUser;

class Tag extends Model
{
    use HasFactory;
    protected $guarded = [
        'tag_id'
    ];
    protected $primaryKey = "tag_id";
    public function tag_groups()
    {
        return $this->hasMany(TagGroup::class, "tag_id", "tag_id");
    }

    public function tag_pages()
    {
        return $this->hasMany(TagPage::class, "tag_id", "tag_id");
    }

    public function tag_posts()
    {
        return $this->hasMany(TagPost::class, "tag_id", "tag_id");
    }

    public function tag_users()
    {
        return $this->hasMany(TagUser::class, "tag_id", "tag_id");
    }
}
