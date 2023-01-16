<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\User;
use App\Models\Post;
use App\Models\Item;
use App\Models\Comment;
use App\Models\Favorite;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'sex',
        'email',
        'password',
        'profile',
        'profile_image_url'
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
    ];
    
    public function posts()   
    {
        return $this->hasMany(Post::class);  
    }
    
    public function items()   
    {
        return $this->hasMany(Item::class);  
    }
    
    public function comments()   
    {
        return $this->hasMany(Comment::class);  
    }
    
    public function favoritingPosts()   
    {
        return $this->belongsToMany(Post::class, 'favorires', 'user_id', 'post_id');  
    }
    
    public function followees() //ユーザーがフォローしている相手の情報を取得
    {
        return $this->belongsToMany(User::class, 'follows', 'follower_id', 'followee_id');
    }

    public function followers() //ユーザーがフォローされている相手の情報を取得
    {
        return $this->belongsToMany(User::class, 'follows', 'followee_id', 'follower_id');
    }
    
    // ユーザーが引数に渡されたユーザーをフォローしていればTureを返す
    public function isFollowing($user_id)
    {
       // exists()でテーブルにレコードが存在しているかをチェック
       return $this->followees()->where('followee_id', $user_id)->exists();
    }
    
    // ユーザーが引数に渡されたユーザーからフォローされていればTrueを返す
    public function isFollowed($user_id)
    {
       return $this->followers()->where('follower_id', $user_id)->exists();
    }
    
    // フォロー中のユーザー数を取得するメソッド
    public function countFollowees()
    {
        return $this->followees()->count();
    }
    
    // フォロワー数を取得するメソッド
    public function countFollowers()
    {
        return $this->followers()->count();
    }
}
