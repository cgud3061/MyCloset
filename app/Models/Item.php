<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Categorie;
use App\Models\Type;
use App\Models\Brand;
use App\Models\Post;

class Item extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'user_id',
        'categorie_id',
        'type_id',
        'brand_id',
        'name',
        'image_url',
        'price',
        'state',
    ];
        
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function categorie()
    {
        return $this->belongsTo(Categorie::class);
    }
    
    public function type()
    {
        return $this->belongsTo(Type::class);
    }
    
    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }
    
    public function posts()
    {
        return $this->belongsToMany(Post::class);
    }
}
