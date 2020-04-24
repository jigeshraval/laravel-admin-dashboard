<?php 

namespace AdLara\Boot;

use Illuminate\Support\Facades\Route;

trait RoutesProvider
{
    protected function mapRoutes()
    {
        $namespace = 'App\Http\Controllers\FrontControllers';
        
        if (app()->app_scope == 'admin') {
            
            $namespace = 'App\Http\Controllers\AdminControllers';

            Route::middleware('web')
                 ->prefix(config('adlara.admin_route'))
                 ->namespace($namespace)
                 ->group(base_path('routes/admin.php'));

        } else {

            $namespace = 'App\Http\Controllers\FrontControllers';

            Route::middleware('web')
                 ->namespace($namespace)
                 ->group(base_path('routes/web.php'));

        }
    }
}