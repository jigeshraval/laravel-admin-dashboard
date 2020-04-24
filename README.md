# AdLara Bootstrap

Laravel VueJS/Vuetify Powered admin, booting up required components that separates Admin and Front and provides rich tools such as Media Library


# Installation Steps 
1. Add "AdLara\Boot\BootstrapServiceProvider::class" in the providers array in config/app.php
2. Make sure database credentials are added in .env file and database is connected with the Laravel Application
3. Delete all the migrations from database/migrations folder
3. php artisan vendor:publis --tag=adlarafullsetup --force
4. npm install 
5. npm run watch
6. Add new middleware 'admin_user' => \App\Http\Middleware\AdminUser::class, in $routeMiddleware array in the file App/Http/Kernel.php 
7. Copy data from Auth.php file to config/auth.php file 
8. Add use AdLara\Boot\RouteProvider; in App\Providers\RouteServiceProvider.php
9. Add use RouteProvider in class RouteServiceProvider
10. in the method "mapWebRoutes" in RouteServiceProvider.php file, just add $this->mapRoutes();
11. php artisan migrate 
12. composer dump-autoload (Pre database seeding)
13. php artisan db:seed --class=PostSeeder
14. Add key '/admin/api/media/upload' in VerifyCSRFToken $except variable
15. composer install invervention/image 
