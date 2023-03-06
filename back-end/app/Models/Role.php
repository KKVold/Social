<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\PageUser;
use App\Models\GroupUser;

class Role extends Model
{
    use HasFactory;
    protected $guarded = [
        'role_id'
    ];
    protected $primaryKey = "role_id";
    public function page_users()
    {
        return $this->hasOne(PageUser::class, "role_id", "role_id");
    }
    public function group_users()
    {
        return $this->hasOne(GroupUser::class, "role_id", "role_id");
    }
}
