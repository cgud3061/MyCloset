<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\User;
use App\Models\Categorie;
use App\Models\Type;
use App\Models\Brand;
use Inertia\Inertia;
use App\Http\Requests\ItemRequest;
use Illuminate\Support\Facades\Auth;
use Cloudinary;

class ItemController extends Controller
{
    // ナビゲーションから自分のマイページにアクセスするメソッド
    public function index(Type $type, Categorie $categorie, Brand $brand)
    {
        // ログインしているユーザー本人の情報
        $user = Auth::user();
        // ログインしているユーザー本人が登録している洋服の情報
        $item = Item::where('user_id', $user->id)->with('categorie', 'type', 'brand')->get();
        
        return Inertia::render('Mycloset/Index',[
            'user'=>$user,
            'followees'=>$user->countFollowees(),
            'followers'=>$user->countFollowers(),
            'items'=>$item,
            'types'=>$type->get(),
            'categories'=>$categorie->get(),
            'brands'=>$brand->get(),
            ]);
    }
    
    // 投稿から投稿者のマイページにアクセスするメソッド
    public function otherIndex(User $user, Type $type, Categorie $categorie, Brand $brand) 
    {
        // 投稿者の所有アイテムの情報を$itemsに格納
        $item = Item::where('user_id', $user->id)->with('categorie', 'type', 'brand')->get();
        
        $otherUser = Auth::user();
        
        // 投稿者をフォローしていればTrue
        $isFollowing = $user->isFollowed($otherUser->id);
       
        return Inertia::render('Mycloset/OtherIndex',[
            // 投稿者のユーザー情報
            'user'=>$user,
            // ログインしているユーザー本人の情報
            'otherUser'=>Auth::user(),
            'isFollowing'=>$isFollowing,
            'followees'=>$user->countFollowees(),
            'followers'=>$user->countFollowers(),
            'items'=>$item,
            'types'=>$type->get(),
            'categories'=>$categorie->get(),
            'brands'=>$brand->get(),
            ]);
    }
    
    public function store(ItemRequest $request, Item $item)
    {
        $user_id = Auth::user()->id;
        // Cloudinaryに画像を送信し，$image_urlに画像のURLを格納している
        $image_url = Cloudinary::upload($request->file->getRealPath())->getSecurePath();
        $input = $request->all();
        $input["user_id"] = $user_id;
        $input["image_url"] = $image_url;
        unset($input["file"]);
        $item->fill($input)->save();
        
        return redirect('/mycloset');
    }
    
    public function update(ItemRequest $request, Item $item)
    {
        $input = $request->all();
        // 画像の変更がある場合のみ実行
        if ($request->file != null) {
            $image_url = Cloudinary::upload($request->file->getRealPath())->getSecurePath();
            $input["image_url"] = $image_url;
            unset($input["file"]);
        }
        
        $item->fill($input)->save();
        
        return redirect('/mycloset');
    }
    
    public function delete(Item $item)
    {
        $item->delete();
        return redirect('/mycloset');
    }
}
