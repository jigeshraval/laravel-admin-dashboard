<?php 

return [

    /*
    |--------------------------------------------------------------------------
    | Admin Route
    |--------------------------------------------------------------------------
    |
    | This Admin Route will be used to define the scope and 
    | split the resources such as, Middleware, Routes, and
    | Controllers across the application
    |
    | If you change this route, please remember to replace this route
    | in app/Http/Middleware/VerifyCSRFToken.php file as this is statically
    | added into the file to except media uploader route
    | 
    */
    
    'admin_route' => 'admin',

    'app_scope' => 'front'

];