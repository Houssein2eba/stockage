<?php

namespace App\Http\Controllers\Products;

use App\Http\Controllers\Controller;
use App\Http\Requests\Products\CategoriesRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CategoriesController extends Controller
{
    public function index(){
        $category=CategoryResource::collection(Category::all());
        // return Inertia::render('Categories/Index', [
        //     'categories' => $category,
        // ]);
        return inertia('Categories/Index', [
            'categories' => $category,
        ]);
    }
    public function store(CategoriesRequest $request){

        Category::create($request->all());

        return back();
    }
}
