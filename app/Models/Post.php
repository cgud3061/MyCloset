<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Comment;
use App\Models\Favorite;
use App\Models\Item;
use App\Models\Tag;
use App\Models\Image;

class Post extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'user_id',
        'title',
        'body',
    ];
    
    protected $appends = [
      'favorites_count', 'favorited_by_user',
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
    
    public function favoritingUsers()
    {
        return $this->belongsToMany(User::class, 'favorites', 'post_id', 'user_id');
    }
    
    public function getFavoritesCountAttribute()
    {
        return $this->favoritingUsers->count();
    }
    
    public function getFavoritedByUserAttribute()
    {
        return $this->favoritingUsers->contains(function ($user) {
            return $user->id === Auth::user()->id;
        });
    }
    
    public function images()
    {
        return $this->hasMany(Image::class);
    }
    
    public function items()
    {
        return $this->belongsToMany(Item::class);
    }
    
    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }
}
