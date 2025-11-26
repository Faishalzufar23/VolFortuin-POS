<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Pos\Cashier;
use App\Models\Sale;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/kasir', Cashier::class)->middleware('auth')->name('kasir');

