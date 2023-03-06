<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Group;
use App\Models\User;


class GroupUser extends Model
{
    use HasFactory;
    protected $guarded = [];
    public function group()
    {
        return $this->belongsTo(User::class, "group_id", "group_id");
    }
    public function user()
    {
        return $this->belongsTo(User::class, "user_id", "user_id");
    }
}
