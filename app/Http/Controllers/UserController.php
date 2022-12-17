<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Follow;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class UserController extends Controller
{
    public function follow (User $followed_user) 
    {
        // フォローを行うユーザー(ログインしているユーザー本人)のid
        $following_user_id = Auth::user()->id;
        // フォローされるユーザーとフォローを行うユーザーのidをfollowテーブルに登録
        $followed_user->followers()->toggle($following_user_id);
        
        return $followed_user->countFollowers();
    }
    
    public function checkFollowers (User $user)
    {
        return Inertia::render('Mycloset/Followers',[
            // 投稿者のユーザー情報
            'user'=>$user,
            'followers'=>$user->followers,
            ]);
    }
    
    public function checkFollowees (User $user)
    {
        return Inertia::render('Mycloset/Followees',[
            // 投稿者のユーザー情報
            'user'=>$user,
            'followees'=>$user->followees,
            ]);
    }
}
