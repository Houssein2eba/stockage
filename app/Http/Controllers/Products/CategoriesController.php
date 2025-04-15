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
        
        return inertia('Categories/Index', [
            'categories' => $category,
        ]);
    }
    public function store(CategoriesRequest $request){

        Category::create($request->all());

        return back();
    }
    public function destroy($id){
        
        $category=Category::find($id);
        if($category){
            $category->delete();
        }
        return back()->withErrors('Category not found');
    }

    public function update(CategoriesRequest $request){
        $category=Category::find($request->id)->update($request->all());
        return back();
    }
}
