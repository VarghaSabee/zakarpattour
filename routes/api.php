<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::post('/user/store/avatar', 'UserAvatarController@store');
Route::get('/user/get/avatar/{user_id}/{size?}', 'UserAvatarController@get');

Route::post('/post/store/image', 'ImagesController@store');
Route::get('/post/get/image/{name}/{size?}', 'ImagesController@get');


Route::group(['middleware' => 'auth:api'], function () {
    Route::post('logout', 'Auth\LoginController@logout');

    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::patch('settings/profile', 'Settings\ProfileController@update');
    Route::patch('settings/password', 'Settings\PasswordController@update');
    Route::delete('settings/destroy', 'Settings\PasswordController@destroy');
});

Route::group(['middleware' => 'guest:api'], function () {
    Route::post('login', 'Auth\LoginController@login');
    Route::post('register', 'Auth\RegisterController@register');

    Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail');
    Route::post('password/reset', 'Auth\ResetPasswordController@reset');

    Route::post('oauth/{provider}', 'Auth\OAuthController@redirectToProvider');
    Route::get('oauth/{provider}/callback', 'Auth\OAuthController@handleProviderCallback')->name('oauth.callback');
});

//    Tour category
Route::group(['prefix' => 'tour/category'], function () {
    Route::put('/store', 'TourCategoryController@store');
    Route::put('/edit', 'TourCategoryController@edit');
    Route::delete('/trash', 'TourCategoryController@destroy');
    Route::post('/show', 'TourCategoryController@show');
    Route::post('/restore', 'TourCategoryController@restoreTrashed');
    Route::get('/', 'TourCategoryController@index');
    Route::get('/trashed', 'TourCategoryController@trashed');
    Route::delete('/remove', 'TourCategoryController@destroyForever');
    Route::get('/list', 'TourCategoryController@listOfCategories');
});
//    Tours
Route::group(['prefix' => 'tour'], function () {
    Route::put('/store', 'TourController@store');
    Route::put('/edit', 'TourController@edit');
    Route::delete('/trash', 'TourController@destroy');
    Route::get('/show/{slug}', 'TourController@show');
    Route::get('/trashed', 'TourController@trashed');
    Route::get('/', 'TourController@index');
    Route::post('/get', 'TourController@get');
});
//    Marker Category
Route::group(['prefix' => 'marker/category'], function () {
    Route::put('/store', 'MarkerCategoryController@store');
    Route::put('/edit', 'MarkerCategoryController@edit');
    Route::delete('/trash', 'MarkerCategoryController@destroy');
    Route::post('/show', 'MarkerCategoryController@show');
    Route::post('/restore', 'MarkerCategoryController@restoreTrashed');
    Route::get('/', 'MarkerCategoryController@index');
    Route::get('/trashed', 'MarkerCategoryController@trashed');
    Route::delete('/remove', 'MarkerCategoryController@destroyForever');
    Route::get('/list', 'MarkerCategoryController@listOfCategories');

});
//    Marker
Route::group(['prefix' => 'marker'], function () {
    Route::put('/store', 'MarkerController@store');
    Route::put('/edit', 'MarkerController@edit');
    Route::delete('/trash', 'MarkerController@destroy');
    Route::get('/show/{slug}', 'MarkerController@show');
    Route::post('/restore', 'MarkerController@restoreTrashed');
    Route::get('/', 'MarkerController@index');
    Route::post('/get', 'MarkerController@get');
    Route::get('/trashed', 'MarkerController@trashed');
    Route::delete('/remove', 'MarkerController@destroyForever');
});


//    Settlement
Route::group(['prefix' => 'settlement'], function () {
    Route::put('/store', 'SettlementController@store');
    Route::put('/edit', 'SettlementController@edit');
    Route::post('/restore', 'SettlementController@restore');
    Route::delete('/trash', 'SettlementController@destroy');
    Route::get('/show/{slug}', 'SettlementController@show');
    Route::get('/', 'SettlementController@index');
    Route::get('/trashed', 'SettlementController@trashed');
});


// Favourite
Route::group(['middleware' => 'auth:api','prefix' => 'favourite'], function () {
    Route::delete('/marker', 'FavouritesController@deleteMarker');
    Route::delete('/tour', 'FavouritesController@deleteTour');

    Route::put('/add/marker', 'FavouritesController@createMarker');
    Route::put('/add/tour', 'FavouritesController@createTour');

    Route::get('/tours', 'FavouritesController@tours');
    Route::get('/markers', 'FavouritesController@markers');
    Route::post('/get', 'FavouritesController@getFavorites');

});

//    Orders
Route::group(['prefix' => 'order'], function () {
    Route::delete('/trash', 'OrdersController@destroy');
    Route::post('/', 'OrdersController@index');
    Route::post('/user', 'OrdersController@user');
    Route::put('/status', 'OrdersController@status');
    Route::post('/store', 'OrdersController@store');

});

// Contact us
Route::group(['prefix' => 'contact'], function () {
    Route::put('/store', 'ContactUsController@store');
    Route::delete('/trash', 'ContactUsController@delete');
    Route::get('/', 'ContactUsController@index');
    Route::get('/archive', 'ContactUsController@archive');
    Route::post('/check', 'ContactUsController@check');
    Route::post('/reply', 'ContactUsController@reply');
});

//Admin

Route::group(['prefix' => 'admin','middleware' => 'guest:admin'], function () {
    Route::post('/login', 'Auth\AdminLoginController@login');
});

Route::group(['prefix' => 'image'], function () {
    Route::post('/upload', 'ImagesController@upload');
    Route::delete('/remove', 'ImagesController@remove');
    Route::get('/show/{type}/{id}/{size?}/{name}', 'ImagesController@show');
    Route::get('/show/{type}/{id}/{name}', 'ImagesController@showOriginal');
    Route::get('/collect/{type}/{id}', 'ImagesController@collect');

    Route::get('/title/{type}/{id}', 'ImagesController@titleImg');
    Route::post('/set/title', 'ImagesController@setTitleImg');
});

Route::group(['prefix' => 'admin','middleware' => 'auth:admin'], function () {
    Route::post('logout', 'Auth\AdminLoginController@logout');

    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::get('/statistic', 'AdminController@index');
    Route::get('/users', 'AdminController@getUsers');
    Route::get('/admins', 'AdminController@getAdmins');
    Route::put('/edit', 'AdminController@edit');
    Route::put('/create', 'AdminController@create');
    Route::post('/activate', 'AdminController@activate');
    Route::delete('/delete', 'AdminController@delete');
});

Route::group(['prefix' => 'pages'], function () {
    Route::get('/', 'PagesController@readContent');
    Route::put('/put', 'PagesController@writeContent');
});

Route::get('mapkey/icons', function () {
    return response()->json(json_decode(file_get_contents(public_path()
        . DIRECTORY_SEPARATOR . 'js' . DIRECTORY_SEPARATOR . 'mapkeyicons.json'),true),200);
});



