<?php

Route::get('/', function () {
    return redirect(config('adlara.admin_route') . '/app/login');
});

Route::get('app', function () {
    return redirect(config('adlara.admin_route') . '/app/posts');
});

Route::get('/app/{url?}/{url2?}/{url3?}/{url4?}/{url5?}', function () {
    return view('dashboard');
});

Route::post('api/employee/login', 'AdminUserController@initProcessLogin');
Route::get('api/employee/logout', 'AdminUserController@initProcessLogout');

Route::get('api/employee/check/login', 'AdminUserController@initProcessCheckLogin');

Route::group(['prefix' => 'api', 'middleware' => 'admin_user'], function () {

    Route::get('post/category/add', 'PostCategoryController@initContentCreate')->name('post_category.add');
    Route::post('post/category/add', 'PostCategoryController@initProcessCreate');
    Route::get('post/category/edit/{id}', 'PostCategoryController@initContentCreate')->name('post_category.edit');
    Route::post('post/category/edit/{id}', 'PostCategoryController@initProcessCreate');
    Route::get('post/category', 'PostCategoryController@initListing')->name('post_category.list');
    Route::get('post/category/delete/{id}', 'PostCategoryController@initProcessDelete')->name('post_category.delete');

    Route::get('post/add', 'PostController@initContentCreate')->name('post.add');
    Route::post('post/add', 'PostController@initProcessCreate');
    Route::get('post/edit/{id}', 'PostController@initContentCreate')->name('post.edit');
    Route::post('post/edit/{id}', 'PostController@initProcessCreate');
    Route::get('post', 'PostController@initListing')->name('post.list');
    Route::get('post/delete/{id}', 'PostController@initProcessDelete')->name('post.delete');

    Route::get('page/add', 'PageController@initContentCreate')->name('page.add');
    Route::post('page/add', 'PageController@initProcessCreate');
    Route::get('page/edit/{id}', 'PageController@initContentCreate')->name('page.edit');
    Route::post('page/edit/{id}', 'PageController@initProcessCreate');
    Route::get('page', 'PageController@initListing')->name('page.list');

    Route::post('change/status', 'PageController@initProcessChangeStatus');
    Route::post('{component}/delete/{id}', 'PageController@initProcessDelete');

    Route::get('media', 'MediaController@initProcessListing');
    Route::post('media/upload', 'MediaController@initProcessUpload');

    Route::get('logout', function () {
        
        \Auth::guard('admin_user')->logout();

        return redirect('/' . config('adlara.admin_route') . '/app/login');

    });
});