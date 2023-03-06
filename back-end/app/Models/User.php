<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use App\Models\Comment;
use App\Models\Post;
use App\Models\Group;
use App\Models\GroupUser;
use App\Models\Page;
use App\Models\PageUser;
use App\Models\TagUser;
use App\Models\ReactionComment;
use App\Models\ReactionPost;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    protected $primaryKey = "user_id";
    public function comments()
    {
        return $this->hasMany(Comment::class, "user_id", "user_id");
    }
    public function Posts()
    {
        return $this->hasMany(Post::class, "user_id", "user_id");
    }
    public function groups()
    {
        return $this->hasMany(Group::class, "user_id", "user_id");
    }
    public function user_groups()
    {
        return $this->hasMany(GroupUser::class, "user_id", "user_id");
    }
    public function pages()
    {
        return $this->hasMany(Page::class, "user_id", "user_id");
    }
    public function user_pages()
    {
        return $this->hasMany(PageUser::class, "user_id", "user_id");
    }
    public function user_tags()
    {
        return $this->hasMany(TagUser::class, "user_id", "user_id");
    }
    public function comment_reactions()
    {
        return $this->hasMany(ReactionComment::class, "user_id", "user_id");
    }
    public function post_reactions()
    {
        return $this->hasMany(ReactionPost::class, "user_id", "user_id");
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    // protected $fillable = [
    //     'first_name',
    //     'email',
    //     'password',
    // ];
    protected $guarded = [
        'user_id'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'birth_date' => 'datetime:Y-m-d'
    ];
}
