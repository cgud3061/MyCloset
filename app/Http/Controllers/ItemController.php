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
    public function index(Item $item, Type $type, Categorie $categorie, Brand $brand)
    {
        return Inertia::render('Mycloset/Index.1',[
            'user'=>Auth::user(),
            'items'=>$item->get(),
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
