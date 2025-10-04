<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::post('/users/login', [AuthController::class, 'login']);
/*
Route::middleware('auth:api')->group(function () {
    Route::get('/users/check', [AuthController::class, 'Check']);
    Route::post('/users/post', [AuthController::class, 'AddUser']);
    Route::get('/users/user/{id}', [AuthController::class, 'GetUser']);
Route::get('/users/logout/{id}', [AuthController::class, 'logout']);
});
*/

//Route::get('/users/user/{id}', [AuthController::class, 'GetUser']);

Route::get('/users/check', [AuthController::class, 'Check']);
Route::post('/users/post', [AuthController::class, 'LatestAddUser']);
//Route::get('/users/post', [UserController::class, 'index']);
Route::get('/users/user/{id}', [AuthController::class, 'GetUser']);
Route::delete('/users/user/{id}', [AuthController::class, 'destroy']);
Route::get('/users/logout/{id}', [AuthController::class, 'logout']);
Route::get('/users/all', [UserController::class, 'index']);


//

// Customers API
//Route::apiResource('customers', CustomerController::class);
//Route::post('/users/post', [CustomerController::class,'store']);
Route::post('/new', [CustomerController::class,'store']);
Route::post('/customers/customer', [CustomerController::class,'store']);
Route::get('/customer-orders', [CustomerController::class,'onHold']);
Route::get('/customers/all', [CustomerController::class,'index']);


// Categories API
//Route::apiResource('categories', CategoryController::class);
Route::post('/categories/category', [CategoryController::class,'store']);
Route::get('/categories/all', [CategoryController::class,'index']);
Route::put('/categories/category', [CategoryController::class,'update']);
Route::post('/categories/category', [CategoryController::class,'store']);
Route::delete('/categories/category/{id}', [CategoryController::class,'destroy']);

// Inventory API
//Route::apiResource('inventory', InventoryController::class);
Route::post('/inventory/product', [InventoryController::class,'store']);
Route::post('/services', [\App\Http\Controllers\ServiceRequest::class,'store']);
Route::get('/inventory/products', [InventoryController::class,'index']);
Route::get('/inventory/product/sku',[InventoryController::class,'getProductSku']);
Route::get('/inventory/product/{id}',[InventoryController::class,'show']);
Route::delete('/inventory/product/{id}',[InventoryController::class,'destroy']);

Route::post('/inventory/product/sku',[InventoryController::class,'getSku']);

// Settings API
//Route::apiResource('settings', SettingController::class);
Route::get('/settings/get', [SettingController::class, 'index']);
Route::post('/settings/post', [SettingController::class, 'store']);



// Transactions API
//Route::apiResource('transactions', TransactionController::class);
Route::post('/new', [TransactionController::class,'store']);
Route::get('/on-hold', [TransactionController::class,'onHold']);
Route::post('/delete', [TransactionController::class,'delete']);

//Route::get('/by-date/{start}/{end}/{user}/{status}/{till}', [TransactionController::class,'index']);
Route::get('/by-date', [TransactionController::class,'ByDate']);
Route::get('/delete',[TransactionController::class,'destroy']);


// Additional routes
Route::get('inventory/low-stock', [InventoryController::class, 'lowStock']);
//Route::get('transactions/by-date/{date}', [TransactionController::class, 'byDate']);
