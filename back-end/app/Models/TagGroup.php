<?php

namespace App\Models;
use App\Models\Group;
use App\Models\Tag;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TagGroup extends Model
{
    use HasFactory;
    protected $guarded = [];
    
    public function group(){
        return $this->belongsTo(Group::class,"group_id","group_id");
    }
    public function tag(){
        return $this->belongsTo(Tag::class,"tag_id","tag_id");
    }
}
