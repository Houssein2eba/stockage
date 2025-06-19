<?php

namespace App\Http\Controllers\Products;

use App\Http\Controllers\Controller;
use App\Http\Requests\Products\CategoriesRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use App\Traits\Sortable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class CategoriesController extends Controller
{
    use Sortable;

    public function index(Request $request)
    {
        $request->validate([
            'search' => 'nullable|string',
            'sort' => 'nullable|string|in:name,description,products_count',
            'direction' => 'nullable|string|in:asc,desc',
            'page' => 'nullable|integer|min:1',
        ],[
            'search.string' => 'Le champ de recherche doit être une chaîne de caractères.',
            'sort.in' => 'Le champ de tri doit être "name", "description" ou "products_count".',
            'direction.in' => 'Le champ de direction doit être "asc" ou "desc".',
            'page.integer' => 'Le numéro de page doit être un entier.',
            'page.min' => 'Le numéro de page doit être au moins 1.',
        ]);
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
            ->paginate(PAGINATION)
            ->withQueryString();

        return inertia('Categories/Index', [
            'categories' => CategoryResource::collection($categories),
            'filters' => $request->only(['search', 'sort', 'direction']),
            'categories_count' => Category::count(),
        ]);
    }

    public function create()
    {
        return Inertia::render('Categories/Create');
    }

    public function store(CategoriesRequest $request){

        try{
            DB::beginTransaction();
            $category = Category::create($request->all());
            $category->refresh();

            DB::commit();
        }catch (\Exception $e){
            DB::rollBack();
            return back()->withErrors('Error creating category: ' . $e->getMessage());
        }

        return back();
    }
    public function destroy($id){

        $category=Category::find($id);
        if($category){

            $category->delete();

        }
        return back()->withErrors("Category n'existe pas");
    }

    public function update(CategoriesRequest $request){
        $category=Category::find($request->id);

        $category->update($request->all());
        $category->refresh();


        return back();
    }
}
