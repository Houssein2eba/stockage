<?php

namespace App\Http\Controllers\Products;

use App\Http\Controllers\Controller;
use App\Http\Requests\Products\ProductsRequest;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\productResource;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ProductsExport;

class ProductsController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'search' => 'nullable|string',
            'category' => 'nullable|exists:categories,id',
            'sort' => 'nullable|string|in:name,price,quantity,created_at,updated_at',
            'direction' => 'nullable|string|in:asc,desc',
            'page' => 'nullable|integer|min:1',
        ]);




        $products = Product::query()
            ->with(['categories', 'orders'])
            ->when($request->search, function ($query) use ($request) {
                $query->where(function($q) use ($request) {
                    $q->where('name', 'like', "%{$request->search}%")
                      ->orWhere('description', 'like', "%{$request->search}%");
                });
            })
            ->when($request->category, function ($query) use ($request) {
                $query->whereHas('categories', function($q) use ($request) {
                    $q->where('categories.id', $request->category);
                });
            })
            ->when($request->sort && $request->direction, function ($query) use ($request) {
                $query->orderBy($request->sort, $request->direction);
            })
            ->when($request->filter, function ($query): void {
                $query->whereRaw('quantity <= min_quantity');
            })
            ->latest()
            ->paginate(PAGINATION)
            ->withQueryString();

        return inertia('Products/Index', [
            'categories' => CategoryResource::collection(Category::all()),
            'products' => ProductResource::collection($products),
            'products_count' => DB::table('products')->count(),
            'filters' => $request->only(['search', 'category', 'sort', 'direction'])
        ]);
    }
    public function show($id)
    {
        $product = Product::with(['categories', 'orders'])->findOrFail($id);
        return inertia('Products/Show', [
            'product' => new productResource($product),
        ]);
    }

    public function create()
    {
        return inertia('Products/Create', [
            'categories' => CategoryResource::collection(Category::all())
        ]);
    }

    public function store(ProductsRequest $request)
    {
        $request->validated();

        DB::transaction(function () use ($request) {
            $url = $request->file('image')->store('products', 'public');

            $product = Product::create([
                'name' => $request->name,
                'description' => $request->description,
                'price' => $request->price,
                'quantity' => $request->quantity,
                'image' => $url,
                'min_quantity' => $request->min_quantity,
            ]);
            $product->categories()->attach(collect($request->category)->pluck('id'));
            $attributes = $product->toArray();
            unset($attributes['id'], $attributes['created_at'], $attributes['updated_at']);
            activity()
                ->causedBy(auth()->user())
                ->performedOn($product)
                ->withProperties(['attributes' => $attributes])
                ->log('Product Created');
        });

        return back();
    }

    public function edit($id)
    {
        $product = Product::with('categories')->findOrFail($id);
        return inertia('Products/Edit', [
            'categories' => CategoryResource::collection(Category::all()),
            'product' => new productResource($product),
        ]);
    }

    public function update(ProductsRequest $request, $id)
    {
        $request->validated();
        $product = Product::findOrFail($id);
        DB::transaction(function () use ($request, $product) {
            $old = $product->toArray();
            if ($request->hasFile('image')) {
                $url = $request->file('image')->store('products', 'public');
                $product->update([
                    'name' => $request->name,
                    'description' => $request->description,
                    'price' => $request->price,
                    'quantity' => $request->quantity,
                    'image' => $url,
                    'min_quantity' => $request->min_quantity,
                ]);
            } else {
                $product->update([
                    'name' => $request->name,
                    'description' => $request->description,
                    'price' => $request->price,
                    'quantity' => $request->quantity,
                    'min_quantity' => $request->min_quantity,
                ]);
            }
            $product->categories()->sync(collect($request->category)->pluck('id'));
            $product->refresh();
            $attributes = $product->toArray();
            unset($old['id'], $old['created_at'], $old['updated_at']);
            unset($attributes['id'], $attributes['created_at'], $attributes['updated_at']);
            activity()
                ->causedBy(auth()->user())
                ->performedOn($product)
                ->withProperties(['old' => $old, 'attributes' => $attributes])
                ->log('Product Updated');
        });

        return to_route('products.index');
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $old = $product->toArray();
        $product->categories()->detach();
        $product->delete();
        unset($old['id'], $old['created_at'], $old['updated_at']);
        activity()
            ->causedBy(auth()->user())
            ->performedOn($product)
            ->withProperties(['old' => $old])
            ->log('Product Deleted');
        return back();
    }

    public function export()
    {
        activity()
            ->causedBy(auth()->user())
            ->log('Products Exported');

            return Excel::download(new ProductsExport(), 'products.xlsx');
    }
}
