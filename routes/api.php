<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
//Route::post('/auth/register', [UserController::class, 'createUser']);  //This was not here!
//Route::post('/auth/login', [UserController::class, 'loginUser']);//This was commented out.

Route::group(['namespace'=>'Api'], function(){     //This is just to use the name space, so you dont have to put it above (This one 'use App\Http\Controllers\Api\UserController;' as it was made global in the RouteServiceProvider.php file).
   
    //Route::post('/login', [UserController::class, 'createUser']);
    Route::post('/login', 'UserController@createUser');   //Due to moving namespace, you have to write it like this instead. Although the above is a regulatr naming convention aka, it is correct when the namespace is written above in the same file.
   
    //Authentication middleware
    Route::group(['middleware'=>['auth:sanctum']], function(){
        //Routes that are protected by middleware
        Route::any('/courseList', 'CourseController@courseList');
        Route::any('/recommendedCourseList', 'CourseController@recommendedCourseList');
        Route::any('/searchCourseList', 'CourseController@searchCourseList');
        Route::any('/courseDetail', 'CourseController@courseDetail');
        Route::any('/checkout', 'PayController@checkout');
        Route::any('/lessonList', 'LessonController@lessonList');
        Route::any('/lessonDetail', 'LessonController@lessonDetail');
        Route::any('/coursesBought', 'CourseController@coursesBought');
        //Just for one item.
        Route::any('/courseBought', 'CourseController@courseBought');
        Route::any('/orderList', 'CourseController@orderList');

        //about author
        Route::any('/courseAuthor', 'CourseController@courseAuthor');
        //Getting all the list created by this author.
        Route::any('/courseListAuthor', 'CourseController@courseListAuthor');
    });
    Route::any('/web_go_hooks', 'PayController@web_go_hooks');  //This is defining an endpoint
});

