<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\GroupUser;
use App\Models\TagGroup;
use App\Models\Post;
use App\Models\User;

class Group extends Model
{
    use HasFactory;
    protected $guarded = [
        'group_id'
    ];
    protected $primaryKey = "group_id";
    public function owner()
    {
        return $this->belongsTo(User::class, "user_id", "user_id");
    }
    public function posts()
    {
        return $this->hasMany(Post::class, "group_id", "group_id");
    }
    public function group_users()
    {
        return $this->hasMany(GroupUser::class, "group_id", "group_id");
    }
    public function group_tags()
    {
        return $this->hasMany(TagGroup::class, "group_id", "group_id");
    }
}
