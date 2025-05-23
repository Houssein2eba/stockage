<?php

namespace App\Http\Controllers\Products;

use App\Http\Controllers\Controller;
use App\Http\Requests\Products\ProductsRequest;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\productResource;
use App\Http\Resources\StockResource;
use App\Models\Category;
use App\Models\Stock;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ProductsExport;

class ProductsController extends Controller
{
    public function __construct(){
        $this->middleware('permission:view_products')->only('index');
    }

public function index(Request $request)
{
    $request->validate([
        'search' => 'nullable|string',
        'stock' => 'nullable|exists:stocks,id',
        'sort' => 'nullable|string|in:name,price,quantity,created_at,updated_at,expiry_date',
        'direction' => 'nullable|string|in:asc,desc',
        'stock_search' => 'nullable|array',
        'expiry_date' => 'nullable|date',
        'page' => 'nullable|integer', // For stocks pagination
    ]);

    // First, get all stocks that have at least one product matching the search criteria
    $stocksQuery = Stock::query()
        ->withCount('products')

        ->when($request->stock, function ($query) use ($request) {
            $query->where('id', $request->stock);
        });


    // Global or stock-specific filtering
    if ($request->search) {
        $stocksQuery->whereHas('products', function ($query) use ($request) {
            $query->where('name', 'like', "%{$request->search}%");
        });
    } elseif ($request->stock_search) {
        $stocksQuery->where(function ($query) use ($request) {
            foreach ($request->stock_search as $stockId => $search) {
                if (!empty($search)) {
                    $query->orWhere(function ($q) use ($stockId, $search) {
                        $q->where('id', $stockId)
                          ->whereHas('products', function ($productQuery) use ($search) {
                              $productQuery->where('name', 'like', "%{$search}%");
                          });
                    });
                }
            }
        });
    }

    $stocks = $stocksQuery->paginate(PAGINATION, ['*'], 'page')->withQueryString();

    // Then for each stock, get only its matching products
    $stocks->getCollection()->transform(function ($stock) use ($request) {
        $stockSearch = $request->stock_search[$stock->id] ?? null;

        $productsQuery = $stock->products()
            ->with('categories')

            ->when($stockSearch, function ($query) use ($stockSearch) {
                $query->where('products.name', 'like', "%{$stockSearch}%");
            })
            ->when(!$stockSearch && $request->search, function ($query) use ($request) {
                $query->where('products.name', 'like', "%{$request->search}%");
            })
            ->when($request->sort && $request->direction, function ($query) use ($request) {

                    $query->orderBy('products.' . $request->sort, $request->direction);

            });

        // Use unique page name for pagination per stock
        $products = $productsQuery->paginate(
            PAGINATION,
            ['*'],
            'stock_' . $stock->id . '_page'
        )->withQueryString();

        $stock->paginated_products = $products;

        return $stock;
    });

    return inertia('Products/Index', [
        'stocks' => [
            'data' => $stocks->getCollection()->map(fn ($stock) => [
                'id' => $stock->id,
                'name' => $stock->name,
                'location' => $stock->location,
                'status' => $stock->status,
                'created_at' => $stock->created_at,
                'updated_at' => $stock->updated_at,
                'products_count' => $stock->products_count,
                'paginated_products' => [
                    'data' => $stock->paginated_products->items(),
                    'links' => [
                        'first' => $stock->paginated_products->url(1),
                        'last' => $stock->paginated_products->url($stock->paginated_products->lastPage()),
                        'prev' => $stock->paginated_products->previousPageUrl(),
                        'next' => $stock->paginated_products->nextPageUrl(),
                    ],
                    'meta' => [
                        'current_page' => $stock->paginated_products->currentPage(),
                        'last_page' => $stock->paginated_products->lastPage(),
                        'from' => $stock->paginated_products->firstItem(),
                        'to' => $stock->paginated_products->lastItem(),
                        'total' => $stock->paginated_products->total(),
                        'links' => $stock->paginated_products->linkCollection()->toArray(),
                    ],
                ],
            ]),
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
        'categories' => CategoryResource::collection(Category::all()),
        'filters' => array_merge(
            $request->only(['search', 'stock', 'sort', 'direction']),
            ['stock_search' => $request->stock_search ?? []]
        ),
    ]);
}

//export excel
public function export(Request $request)
{
    $request->validate([
        'search' => 'nullable|string',
        'stock' => 'nullable|exists:stocks,id',
        'stock_search' => 'nullable|array',
    ]);

    // Create a new spreadsheet
    $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle('Stocks and Products');

    // Set headers
    $sheet->setCellValue('A1', 'Stock Name');
    $sheet->setCellValue('B1', 'Stock Location');
    $sheet->setCellValue('C1', 'Product Name');
    $sheet->setCellValue('D1', 'Product Price');
    $sheet->setCellValue('E1', 'Product Quantity');
    $sheet->setCellValue('F1', 'Categories');

    // Style the header row
    $headerStyle = [
        'font' => [
            'bold' => true,
        ],
        'fill' => [
            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
            'startColor' => [
                'rgb' => 'E0E0E0',
            ],
        ],
    ];
    $sheet->getStyle('A1:F1')->applyFromArray($headerStyle);

    // First, get all stocks that have at least one product matching the search criteria
    $stocksQuery = Stock::query()
        ->when($request->stock, function ($query) use ($request) {
            $query->where('id', $request->stock);
        });

    // Filter stocks to only include those with matching products
    if ($request->search) {
        $stocksQuery->whereHas('products', function ($query) use ($request) {
            $query->where('name', 'like', "%{$request->search}%");
        });
    } elseif ($request->stock_search) {
        // Handle stock-specific searches
        $stocksQuery->where(function ($query) use ($request) {
            foreach ($request->stock_search as $stockId => $search) {
                if (!empty($search)) {
                    $query->orWhere(function ($q) use ($stockId, $search) {
                        $q->where('id', $stockId)
                          ->whereHas('products', function ($productQuery) use ($search) {
                              $productQuery->where('name', 'like', "%{$search}%");
                          });
                    });
                }
            }
        });
    }

    $stocks = $stocksQuery->get();

    // Row counter
    $row = 2;

    // For each stock, get ONLY its products that match the search criteria
    foreach ($stocks as $stock) {
        // Get stock-specific search query if it exists
        $stockSearch = $request->stock_search[$stock->id] ?? null;

        $productsQuery = $stock->products()
            ->with('categories')
            ->when($stockSearch, function ($query) use ($stockSearch) {
                // Use stock-specific search if available
                $query->where('name', 'like', "%{$stockSearch}%");
            })
            ->when(!$stockSearch && $request->search, function ($query) use ($request) {
                // Fall back to global search if no stock-specific search
                $query->where('name', 'like', "%{$request->search}%");
            });

        $products = $productsQuery->get();

        foreach ($products as $product) {
            // Stock information
            $sheet->setCellValue('A' . $row, $stock->name);
            $sheet->setCellValue('B' . $row, $stock->location);

            // Product information
            $sheet->setCellValue('C' . $row, $product->name);
            $sheet->setCellValue('D' . $row, $product->price);

            // Get quantity from pivot table
            $quantity = $product->pivot->quantity ?? 0;
            $sheet->setCellValue('E' . $row, $quantity);

            // Categories as comma-separated list
            $categories = $product->categories->pluck('name')->implode(', ');
            $sheet->setCellValue('F' . $row, $categories);

            $row++;
        }
    }

    // Auto-size columns
    foreach (range('A', 'F') as $column) {
        $sheet->getColumnDimension($column)->setAutoSize(true);
    }

    // Create Excel file
    $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
    $filename = 'stocks_products_export_' . date('Y-m-d_H-i-s') . '.xlsx';
    $tempPath = storage_path('app/public/exports/' . $filename);

    // Ensure the directory exists
    if (!file_exists(dirname($tempPath))) {
        mkdir(dirname($tempPath), 0755, true);
    }

    // Save the file
    $writer->save($tempPath);

    // Return the file as download
    return response()->download($tempPath, $filename, [
        'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
    ])->deleteFileAfterSend(true);
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
            'stocks' => StockResource::collection(Stock::all()),
            'categories' => CategoryResource::collection(Category::all())
        ]);
    }

    public function store(ProductsRequest $request)
{
    DB::transaction(function () use ($request) {
        $url = $request->hasFile('image')
            ? $request->file('image')->store('products', 'public')
            : 'products/product.png';

        // Create the product
        $product = Product::create([
            'name'        => $request->name,
            'description' => $request->description,
            'price'       => $request->price,
            'cost'        => $request->cost,
            'image'       => $url,
        ]);

        // Attach categories
        $product->categories()->attach(
            collect($request->category)->pluck('id')
        );

        // Attach stocks with pivot data
        // $stockData = [];

        foreach ($request->stockQuantities as $stockEntry) {
        $product->stocks()->attach($stockEntry['stock']['id'], [
            'quantity'    => $stockEntry['quantity'],
            'expiry_date'  =>$request['expiry_date'] , // Optional
        ]);

            // $stock = $stockEntry['stock'];
            // $stockId = $stock['id'];
            // $stockData[$stockId] = [
            //     'quantity'    => $stockEntry['quantity'],
            //     'expiry_date'  =>$request['expiry_date'] , // Optional
            // ];
        }

        // $product->stocks()->attach($stockData);

        // Activity log
        $attributes = $product->toArray();
        unset($attributes['id'], $attributes['created_at'], $attributes['updated_at']);

        activity()
            ->causedBy(auth()->user())
            ->performedOn($product)
            ->withProperties(['attributes' => $attributes])
            ->log('Product Created');
    });

    return to_route('products.index');
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

                    'image' => $url,

                    'cost'=> $request->cost,
                ]);
            } else {
                $product->update([
                    'name' => $request->name,
                    'description' => $request->description,
                    'price' => $request->price,


                    'cost'=> $request->cost,

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



            if ($product->image && $product->image !== 'products/product.png') {
                // Delete the image from storage
                Storage::disk('public')->delete($product->image);
            }

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

public  function lowStock(Request $request){
    $stocksQuery = Stock::query()
        ->withCount('lows');

        $stocks = $stocksQuery->paginate(PAGINATION, ['*'], 'page')->withQueryString();

    // Then for each stock, get only its matching products
    $stocks->getCollection()->transform(function ($stock) use ($request) {
        $stockSearch = $request->stock_search[$stock->id] ?? null;

        $productsQuery = $stock->lows()
            ->with('categories')

            ->when($stockSearch, function ($query) use ($stockSearch) {
                $query->where('products.name', 'like', "%{$stockSearch}%");
            })
            ->when(!$stockSearch && $request->search, function ($query) use ($request) {
                $query->where('products.name', 'like', "%{$request->search}%");
            })
            ->when($request->sort && $request->direction, function ($query) use ($request) {

                    $query->orderBy('products.' . $request->sort, $request->direction);

            });

        // Use unique page name for pagination per stock
        $products = $productsQuery->paginate(
            PAGINATION,
            ['*'],
            'stock_' . $stock->id . '_page'
        )->withQueryString();

        $stock->paginated_lows = $products;

        return $stock;
    });


    return Inertia::render('Products/Low',[
        'stocks' => [
            'data' => $stocks->getCollection()->map(fn ($stock) => [
                'id' => $stock->id,
                'name' => $stock->name,
                'location' => $stock->location,
                'status' => $stock->status,
                'created_at' => $stock->created_at,
                'updated_at' => $stock->updated_at,
                'products_count' => $stock->products_count,
                'paginated_lows' => [
                    'data' => $stock->paginated_lows->items(),
                    'links' => [
                        'first' => $stock->paginated_lows->url(1),
                        'last' => $stock->paginated_lows->url($stock->paginated_lows->lastPage()),
                        'prev' => $stock->paginated_lows->previousPageUrl(),
                        'next' => $stock->paginated_lows->nextPageUrl(),
                    ],
                    'meta' => [
                        'current_page' => $stock->paginated_lows->currentPage(),
                        'last_page' => $stock->paginated_lows->lastPage(),
                        'from' => $stock->paginated_lows->firstItem(),
                        'to' => $stock->paginated_lows->lastItem(),
                        'total' => $stock->paginated_lows->total(),
                        'links' => $stock->paginated_lows->linkCollection()->toArray(),
                    ],
                ],
            ]),
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
        'filters'=>[
            'search'=>request('search'),]
    ]);
}

    // public function export()
    // {
    //     activity()
    //         ->causedBy(auth()->user())
    //         ->log('Products Exported');

    //         return Excel::download(new ProductsExport(), 'products.xlsx');
    // }
}
