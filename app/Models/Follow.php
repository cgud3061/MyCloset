<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use Illuminate\Database\Eloquent\SoftDeletes;

class Follow extends Model
{
    use HasFactory;
    use SoftDeletes;
    
    protected $fillable = [
        'followee_id',
        'follower_id',
    ];
}
