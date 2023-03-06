<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Page;
use App\Models\Tag;


class TagPage extends Model
{
    use HasFactory;
    protected $guarded = [];
    public function page()
    {
        return $this->belongsTo(Page::class, "page_id", "page_id");
    }
    public function tag()
    {
        return $this->belongsTo(Tag::class, "tag_id", "tag_id");
    }
}
