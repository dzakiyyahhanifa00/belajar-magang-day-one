<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;


Route::get('/', function () {
    return view('welcome');
});

Route::get('/products-ui', function () {
    return view('products');
});

Route::get('/categories-ui', function () {
    return view('category');
});