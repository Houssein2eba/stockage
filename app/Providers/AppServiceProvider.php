<?php

namespace App\Providers;

use App\Models\Admin;
use App\Models\Product;
use App\Models\ProductStock;
use App\Observers\ProductObserver;
use App\Observers\ProductStockObserver;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Vite::prefetch(concurrency: 3);

        Gate::before(function($user,$ability){
            if($user->hasRole('admin')){
                return true;
            }
            return false;
        });

       JsonResource::withoutWrapping();

       Model::automaticallyEagerLoadRelationships();

       Product::observe(ProductObserver::class);
       ProductStock::observe(ProductStockObserver::class);





    }
}
