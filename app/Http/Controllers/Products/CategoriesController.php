<?php

namespace App\Http\Controllers\Products;

use App\Http\Controllers\Controller;
use App\Http\Requests\Products\CategoriesRequest;
use App\Models\Category;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CategoriesController extends Controller
{
    public function create(){
        return Inertia::render('Categories/Create');
    }
    public function store(CategoriesRequest $request){

        Category::create($request->all());

        return back();
    }
}
