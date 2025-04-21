<?php

namespace App\Http\Controllers\Products;

use App\Http\Controllers\Controller;
use App\Http\Requests\Products\CategoriesRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use App\Traits\Sortable;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CategoriesController extends Controller
{
    use Sortable;

    public function index(Request $request)
    {
        $categories = Category::query()
            ->withCount('products')
            ->when($request->search, function ($query) use ($request) {
                $query->where('name', 'like', "%{$request->search}%")
                    ->orWhere('description', 'like', "%{$request->search}%");
            })
            ->when($request->sort && $request->direction, function ($query) use ($request) {
                if ($request->sort === 'products_count') {
                    $query->orderBy('products_count', $request->direction);
                } else {
                    $query->orderBy($request->sort, $request->direction);
                }
            }, function ($query) {
                $query->latest();
            })
            ->get();

        return inertia('Categories/Index', [
            'categories' => CategoryResource::collection($categories),
            'filters' => $request->only(['search', 'sort', 'direction'])
        ]);
    }

    public function create()
    {
        return Inertia::render('Categories/Create');
    }

    public function store(CategoriesRequest $request){

        Category::create([
            'name'=>$request->name,
            'description'=>$request->description,
        ]);

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
