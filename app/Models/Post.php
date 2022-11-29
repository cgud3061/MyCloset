<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Comment;
use App\Models\Favorite;
use App\Models\Item;
use App\Models\Tag;

class Post extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'user_id',
        'title',
        'body',
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
    
    public function favorites()
    {
        return $this->hasMany(Favorite::class);
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
