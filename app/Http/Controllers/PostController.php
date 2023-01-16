<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use App\Models\User;
use App\Models\Post;
use App\Models\Image;

class PostController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $please_follow = $user->countFollowees() === 0;
        // ユーザーがフォローしているユーザーのidを取得
        $followee_id = $user->followees->pluck('id')->toArray();
        //　自分のidを追加
        array_push($followee_id, $user->id);
        // フォローしているユーザ(自分も含む)の投稿を取得
        $post = Post::whereIn('user_id', $followee_id)->with('images', 'user')->get();
        $item = $user->items()->get();
        
        return Inertia::render('Mycloset/TimeLine',[
            'user'=>$user,
            'pleaseFollow'=>$please_follow,
            'posts'=>$post,
            'items'=>$item,
            ]);
    }
    
    public function recomend()
    {
        $user = Auth::user();
        if ($user->countFollowees() === 0){
            $post = Post::where('user_id', '!=', $user->id)->orderBy('created_at', 'DESC')->limit(20)->with('images', 'user')->get();
        } else {
            // フォローしているユーザーのidを取得
            $followee_id = $user->followees->pluck('id')->toArray();
            // フォローしているユーザーの情報を取得
            $followees = User::whereIn('id', $followee_id)->get();
            // おすすめするユーザーのidを追加する配列
            $recomended_user_id = array();
            
            foreach ($followees as $followee){
                // フォローしているユーザーがフォローしているユーザーのidを$followee_followee_idとする
                $followee_followee_id = $followee->followees->pluck('id')->toArray();
                foreach ($followee_followee_id as $id){
                    array_push($recomended_user_id, $id);
                }
            }
            // 重複を削除
            $recomended_user_id = array_unique($recomended_user_id);
            // ユーザー本人のidを削除
            $recomended_user_id = array_diff($recomended_user_id, array($user->id));
            // 削除した分の空白を詰める
            $recomended_user_id = array_values($recomended_user_id);
            
            $post = Post::whereIn('user_id', $recomended_user_id)->with('images', 'user')->get();
            
            if ($post == null) {
                $post = Post::where('user_id', '!=', $user->id)->orderBy('created_at', 'DESC')->limit(20)->with('images', 'user')->get();
            }
        }
        
        $item =$user->items()->get();
        
        return Inertia::render('Dashboard',[
            'user'=>$user,
            'posts'=>$post,
            'items'=>$item,
            ]);
    }
    
    public function myPosts()
    {
        // ログインしているユーザー本人の情報
        $user = Auth::user();
        // ログインしているユーザー本人が登録している洋服の情報
        $posts = Post::where('user_id', $user->id)->with('images')->get();
        $items = $user->items()->get();
        
        return Inertia::render('Mycloset/IndexPosts',[
            'user'=>$user,
            'followees'=>$user->countFollowees(),
            'followers'=>$user->countFollowers(),
            'posts'=>$posts,
            'items'=>$items,
            ]);
    }
    
    public function otherPosts(User $user)
    {
        // ログインしているユーザー本人の情報
        $otherUser = Auth::user();
        // ログインしているユーザー本人が登録している洋服の情報
        $posts = Post::where('user_id', $user->id)->with('images')->get();
        
        $isFollowing = $user->isFollowed($otherUser->id);
        
        return Inertia::render('Mycloset/OtherIndexPosts',[
            'user'=>$user,
            'otherUser'=>$otherUser,
            'isFollowing'=>$isFollowing,
            'followees'=>$user->countFollowees(),
            'followers'=>$user->countFollowers(),
            'posts'=>$posts,
            ]);
    }
    
    public function store(User $user, Request $request)
    {
        $input = $request->all();
        $urls = $input['images'];
        
        $post=Post::create([
            'user_id'=>$user->id,
            'title'=>$input['title'],
            'body'=>$input['body'],
        ]);
        
        foreach( $urls as $url){
            Image::create([
                'post_id'=>$post->id,
                'image_url'=>$url
            ]);
        }

    }
    
    public function update(Request $request, Post $post)
    {
        $input = $request->all();
        
        $urls = $input['images'];

        unset($input['images']);
        
        $post->fill($input)->save();
        
        if ($urls != null) {
            // 元の写真を削除
            Image::where('post_id', $post->id)->delete();
            
            // 新しい写真を登録
            foreach( $urls as $url){
                Image::create([
                    'post_id'=>$post->id,
                    'image_url'=>$url
                ]);
            }
        }
        
        return redirect('/myposts');
    }
    
    public function favorites (Post $favorited_post)
    {
        // フォローを行うユーザー(ログインしているユーザー本人)のid
        $favoriting_user_id = Auth::user()->id;
        // フォローされるユーザーとフォローを行うユーザーのidをfollowテーブルに登録
        $favorited_post->favoritingUsers()->toggle($favoriting_user_id);
        
        return $favorited_post;
    }
    
    public function delete(Post $post)
    {
        $post->favoritingUsers()->detach();
        $post->images()->delete();
        $post->delete();
        return redirect('/myposts');
    }
}
