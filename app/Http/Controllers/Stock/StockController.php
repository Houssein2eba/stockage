<?php

namespace App\Http\Controllers\Stock;

use App\Http\Controllers\Controller;
use App\Http\Resources\productResource;
use App\Http\Resources\StockResource;
use App\Models\Stock;
use Illuminate\Http\Request;
use Inertia\Inertia;

class StockController extends Controller
{
public function index(Request $request)
{
    $stocks = Stock::query()
        ->when($request->search, function ($query, $search) {
            $query->where('name', 'LIKE', "%{$search}%");
        })
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
        'search' => 'nullable|string',
        'sort' => 'nullable|string',
        'direction' => 'nullable|in:asc,desc',
    ]);

    $stock = Stock::findOrFail($id);
    $productPage = $request->input('product_page', 1);

    // Paginate products of the given stock with categories loaded
    $paginatedProducts = $stock->products()
        ->with('categories')
        ->when($request->search, function ($query, $search) {
            $query->where('name', 'LIKE', "%{$search}%");
        })
        ->when($request->sort, function ($query, $sort) use ($request) {
            $query->orderBy($sort, $request->direction ?? 'asc');
        })
        ->paginate(PAGINATION, ['*'], 'product_page', $productPage)
        ->withQueryString();

    // Prepare products data with pagination info
    $productsData = [
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

    return Inertia::render('Stock/Show', [
        'stock' => new StockResource($stock),
        'products' => $productsData,
        'productPage' => $productPage,
    ]);
}

}
