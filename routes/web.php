<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'x-ray::x-ray')->name('x-ray.index');
