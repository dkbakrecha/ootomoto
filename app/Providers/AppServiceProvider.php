<?php

namespace App\Providers;

use App\Category;
use App\Service;
use App\ShopService;
use App\Area;
use App\User;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;

use View;
use Auth;

class AppServiceProvider extends ServiceProvider {

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot() {
        Schema::defaultStringLength(191);

        view()->composer('*', function ($view) {
            //Area List
            $areaList = Area::OrderBy('name', 'ASC')->pluck('name', 'id');
            View::share('areaList', $areaList);

            //Service Master List
            $services = Service::OrderBy('name', 'ASC')->pluck('name', 'id');
            View::share('services', $services);

            //Shop List -- Used in Supervisor create
            $shopList = User::OrderBy('name', 'ASC')
                    ->where('status', '=', 1)
                    ->where('user_type', '=', 0)
                    ->pluck('name', 'id');
            View::share('shopList', $shopList);

            $shop = Auth::guard('web')->user();
            if (!empty($shop) && $shop->user_type == 0) {
                $shop_services = ShopService::get()
                        //->orderBy('name', 'ASC')
                        ->where('shop_id', '=', $shop->id);

                View::share('shop_services', $shop_services);
            } else if (!empty($shop) && $shop->user_type == 1) {
                $shop_services = ShopService::get()
                        //->orderBy('name', 'ASC')
                        ->where('shop_id', '=', $shop->shop_id);

                View::share('shop_services', $shop_services);
            }
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register() {
        //
    }

}
