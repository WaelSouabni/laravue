<?php

use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use \App\Laravue\Faker;
use \App\Laravue\JsonResponse;
use \App\Laravue\Acl;

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

Route::namespace('Api')->group(function() {
    Route::post('auth/login', 'AuthController@login');
    

   
    Route::group(['middleware' => 'auth:sanctum'], function () {
        // Auth routes
        Route::get('auth/user', 'AuthController@user');
        Route::post('auth/logout', 'AuthController@logout');

        Route::get('/user', function (Request $request) {
            return new UserResource($request->user());
        });

        // Api resource routes
        Route::apiResource('roles', 'RoleController')->middleware('permission:' . Acl::PERMISSION_PERMISSION_MANAGE);
        Route::apiResource('users', 'UserController')->middleware('permission:' . Acl::PERMISSION_USER_MANAGE);
        Route::apiResource('permissions', 'PermissionController')->middleware('permission:' . Acl::PERMISSION_PERMISSION_MANAGE);


        // Custom routes
        Route::put('users/{user}', 'UserController@update');
        Route::get('users/{user}/permissions', 'UserController@permissions')->middleware('permission:' . Acl::PERMISSION_PERMISSION_MANAGE);
        Route::put('users/{user}/permissions', 'UserController@updatePermissions')->middleware('permission:' .Acl::PERMISSION_PERMISSION_MANAGE);
        Route::get('roles/{role}/permissions', 'RoleController@permissions')->middleware('permission:' . Acl::PERMISSION_PERMISSION_MANAGE);
    });
});
//

       

Route::get('usersList', [
    'uses' => 'Api\UserController@users'
   ])->name('users.list');

 //Packages
 Route::apiResource('packages', 'Api\PackageController', ['except' => ['index']]);
      
 Route::get('packages/{request?}', [
    'uses' => 'Api\PackageController@index'
   ])->name('packages.index');
   Route::get('packages/publier/{id}', 'Api\PackageController@publier')->name('packages.publier');
   Route::get('packages/masquer/{id}', 'Api\PackageController@masquer')->name('packages.masquer');

   Route::get('PackagesList', [
    'uses' => 'Api\PackageController@packages'
   ])->name('packages.list');
   Route::get('packages/users/{id}', 'Api\PelerinController@usersPackages')->name('packages.userslist');
   Route::get('listePelerins/{id}', [
    'uses' => 'Api\PackageController@listePelerins'
   ])->name('listePelerins.index');
   //Pelerins controller
 Route::apiResource('pelerins', 'Api\PelerinController', ['except' => ['index']]);
  
 Route::get('pelerins/{request?}', [
    'uses' => 'Api\PelerinController@index'
   ])->name('pelerins.index');
   Route::get('pelerins/valider/{id}', 'Api\PelerinController@valider')->name('pelerins.valider');
   Route::get('pelerins/invalider/{id}', 'Api\PelerinController@invalider')->name('pelerins.invalider');

   //Packages liste dèun pelerins
   Route::get('listePackagesPelerins/{id}', [
    'uses' => 'Api\PelerinController@PackagesUser'
   ])->name('listePackagesPelerins.list');
  
   
   
   //accompgnateur controller
 Route::apiResource('accompgnateurs', 'Api\AccompagnateurController', ['except' => ['index']]);
  
 Route::get('accompgnateurs/{request?}', [
    'uses' => 'Api\AccompagnateurController@index'
   ])->name('accompgnateurs.index');

   

     //Paiement
 Route::apiResource('paiements', 'Api\PaiementController', ['except' => ['index']]);
  
 Route::get('paiements/{request?}', [
    'uses' => 'Api\PaiementController@index'
   ])->name('paiements.index');



//Messages
Route::apiResource('messages', 'Api\MessageController');
  
   
   //dashbord 
Route::get('page/dashbord', 'Api\PageController@statisque');

//mobile
Route::group(['prefix' => 'Mobile'], function () {
    Route::apiResource('MobileSeConnecter', 'Api\MobileUserController');
    Route::post('/login', 'Api\MobileUserController@login');
    Route::get('/test', 'Api\MobileUserController@test');
    Route::post('/register', 'Api\MobileUserController@register');
    Route::get('/logout', 'Api\MobileUserController@logout')->middleware('auth:api');
    Route::get('/accompg', 'Api\MobileUserController@accompagnateur');
    Route::get('/vol', 'Api\MobileUserController@vol');
});
  

