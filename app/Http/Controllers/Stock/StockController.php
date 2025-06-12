<?php

namespace App\Http\Controllers\Stock;

use App\Http\Controllers\Controller;
use App\Http\Resources\MovementResource;
use App\Http\Resources\productResource;
use App\Http\Resources\StockResource;
use App\Models\ProductStock;
use App\Models\Stock;
use Illuminate\Http\Request;
use Inertia\Inertia;

class StockController extends Controller
{
public function index(Request $request)
{
    if($request ->wantsJson()){
        $stocks=Stock::with('products')
        ->cursorPaginate(PAGINATION, ['*'], 'cursor', $request->cursor ?? null)
        ->withQueryString()
        ;
        return response()->json([
             'stocks' => StockResource::collection($stocks),
             'meta'=> [
             'next_cursor' => $stocks->nextCursor()?->encode(),
             'per_page' => $stocks->perPage(),
             'has_more' => $stocks->hasMorePages(),
             ]
        ]);
    }
    $stocks = Stock::query()
        ->when($request->search, function ($query, $search) {
            $query->where('name', 'LIKE', "%{$search}%");
        })
        ->latest()
        ->paginate(PAGINATION)
        ->withQueryString();

    // Transform each stock to include paginated products only if selected
    $stocks->getCollection()->transform(function ($stock) use ($request) {
        $pageKey = 'product_page_' . $stock->id;
        $productPage = $request->input($pageKey, 1);

        if ($request->input('stock_id') == $stock->id) {
            $paginatedProducts = $stock->products()

                ->with('categories')
                ->paginate(PAGINATION, ['*'], $pageKey, $productPage)
                ->withQueryString();

            $stock->paginated_products = [
                'data' => ProductResource::collection($paginatedProducts->items()),
                'links' => [
                    'first' => $paginatedProducts->url(1),
                    'last' => $paginatedProducts->url($paginatedProducts->lastPage()),
                    'prev' => $paginatedProducts->previousPageUrl(),
                    'next' => $paginatedProducts->nextPageUrl(),
                ],
                'meta' => [
                    'current_page' => $paginatedProducts->currentPage(),
                    'last_page' => $paginatedProducts->lastPage(),
                    'from' => $paginatedProducts->firstItem(),
                    'to' => $paginatedProducts->lastItem(),
                    'total' => $paginatedProducts->total(),
                    'links' => $paginatedProducts->linkCollection()->toArray(),
                ],
            ];
        } else {
            $stock->paginated_products = null;
        }

        return $stock;
    });


    return Inertia::render('Stock/Index', [
        'stocks' => [
            'data' => StockResource::collection($stocks->items()),
            'links' => [
                'first' => $stocks->url(1),
                'last' => $stocks->url($stocks->lastPage()),
                'prev' => $stocks->previousPageUrl(),
                'next' => $stocks->nextPageUrl(),
            ],
            'meta' => [
                'current_page' => $stocks->currentPage(),
                'last_page' => $stocks->lastPage(),
                'from' => $stocks->firstItem(),
                'to' => $stocks->lastItem(),
                'total' => $stocks->total(),
                'links' => $stocks->linkCollection()->toArray(),
            ],
        ],
        'search' => $request->search,
    ]);
}



    public function show(Request $request, $id)
{

    $request->validate([
        'search' => 'nullable|string|max:255',
        'date' => 'nullable|date',
        'type' => 'nullable|string|in:in,out',
        'sort' => 'nullable|string|',
        'direction' => 'nullable|string|in:asc,desc',
    ]);
    $stock = Stock::findOrFail($id);

    $movements = ProductStock::with(['product'])
        ->where('stock_id', $id)
        ->when($request->search, function ($query, $search) {
            $query->whereHas('product', function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%");
            });
            })
         ->when($request->date, function ($query, $date) {
                $query->whereDate('stock_date', $date);
            })
            ->when($request->type, function ($query, $type) {
                $query->where('type', $type);
            })
            ->when($request->sort =='quantity', function ($query) use ($request) {
                $query->orderBy('products_quantity', $request->direction ?? 'asc');
            })
            ->when($request->sort, function ($query, $sort) use ($request) {
                $query->orderBy($sort, $request->direction ?? 'asc');
            }, function ($query) {
                $query->latest();
            })
            ->latest()
            ->orderBy('stock_date', 'desc')
        ->paginate(10)
        ->withQueryString();

    return Inertia::render('Stock/Show', [
        'movements' => MovementResource::collection($movements),
        'stock' => $stock,
        'filters' => $request->only(['search', 'date'])
    ]);
}
public function create()
{
    return Inertia::render('Stock/Create');

}

public function store(Request $request)
{

    $request->validate([
        'name' => 'required|string|max:255',
        'location' => 'required|string|max:255',
    ]);

    $stock = Stock::create($request->only('name', 'location'));

    return back();
}
}
