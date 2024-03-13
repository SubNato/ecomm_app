<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::group(['namespace'=>'Teacher'], function(){
    Route::any('/login', 'TeacherController@login');
});