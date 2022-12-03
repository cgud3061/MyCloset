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

class ItemController extends Controller
{
    public function index(Item $item, Type $type, Categorie $categorie, Brand $brand)
    {
        return Inertia::render('Mycloset/Index',[
            'items'=>$item->get(),
            'types'=>$type->get(),
            'categories'=>$categorie->get(),
            'brands'=>$brand->get(),
            ]);
    }
    
    public function store(ItemRequest $request, Item $item)
    {
        $user_id = Auth::user()->id;
        $input = $request->all();
        $input["user_id"] = $user_id;
        $item->fill($input)->save();
        return redirect('/mycloset');
    }
}
