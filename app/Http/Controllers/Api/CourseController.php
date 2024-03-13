<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\Order;
use Illuminate\Support\Facades\DB;

class CourseController extends Controller
{
    //Here we are returning all the course list
    public function courseList(){

        //Select methods select the fields without returning them. 
        try{
            $result = Course::select('name', 'thumbnail', 'lesson_num', 'price', 'id')->get();    //The get method returns them.

        
        return response()->json([
            'code' => 200,
            'msg' => 'My course list is here',
            'data' => $result   //This is also Json data.
        ], 200);
        }catch(\Throwable $throw){
            return response()->json([
                'code'=>500,
                'msg'=> 'The column does not exist or you have a syntax error',
                'data'=> $throw->getMessage()
            ],500);
        }
    }

    //REMEMBER TO ALWAYS ADD THE ENDPOINT TO THE API FILES! VERY IMPORTANT.

    //Here we are returning all the Recommended course list
    public function recommendedCourseList(){

        //Select methods select the fields without returning them. 
        try{
            $result = Course::select('name', 'thumbnail', 'lesson_num', 'price', 'id')->where('recommended', '=', 1)
            ->get();    //The get method returns them.

        
        return response()->json([
            'code' => 200,
            'msg' => 'My recommended course list is here',
            'data' => $result   //This is also Json data.
        ], 200);
        }catch(\Throwable $throw){
            return response()->json([
                'code'=>500,
                'msg'=> 'The column does not exist or you have a syntax error',
                'data'=> $throw->getMessage()
            ],500);
        }
    }

    //Here we are returning all the searched course list
    public function searchCourseList(Request $request){                        //Working with RECIEVING INFORMATION from the FRONTEND exciting stuff!


        $search = $request->search;

        //Select methods select the fields without returning them. 
        try{
            $result = Course::select('name', 'thumbnail', 'lesson_num')
            ->where('name', "like", '%'.$search.'%')      //Here we are querying the database. So it is done in this format, the name (from the database column name), using the like operator, then the value being recieved form the frontend it compares it to, then returns the most similar (Not exact) match.
            ->get();    //The get method returns them.

        
        return response()->json([
            'code' => 200,
            'msg' => 'My searched course list is here',
            'data' => $result   //This is also Json data.
        ], 200);
        }catch(\Throwable $throw){
            return response()->json([
                'code'=>500,
                'msg'=> 'The column does not exist or you have a syntax error',
                'data'=> $throw->getMessage()
            ],500);
        }
    }

    //Returning a course detail.
    public function courseDetail(Request $request){
        //Course id
        $id = $request->id;
        //Select methods select the fields without returning them. 
        try{
            $result = Course::where('id', '=', $id)->select(
                'id',
                'name', 
                //This is teacher's token, not the active users
                'user_token',
                'description',
                'price',
                'lesson_num',
                'video_length',
                'thumbnail',  
                'price', 
                'downloadable_resources'
                
                )
                    ->first();    //The get method returns a collection of items, first method returns one item, the first item found or recieved.

        
        return response()->json([
            'code' => 200,
            'msg' => 'My course detail is here',
            'data' => $result   //This is also Json data.
        ], 200);
        }catch(\Throwable $throw){
            return response()->json([
                'code'=>500,
                'msg'=> 'The column does not exist or you have a syntax error',
                'data'=> $throw->getMessage()
            ],500);
        }
    }

    //This is for checking of a singular course was bought.
    public function courseBought(Request $request){
        try{
           
                $orderMap = [];

            $orderMap['course_id'] = $request->id;
            $orderMap['user_token'] = $request->user()->token;
            $orderMap['status'] = 1;
            
            /**
             * If the order has been placed before or not.
             * So we need order model/table to figure it out or to know.
             */


            $orderRes = Order::where($orderMap)->first();
            if(!empty($orderRes)){     //If the query is not empty, it means that you have bought the course.
                return response()->json([
                    'code' => 200,
                    'msg' => "success",
                    'data' => ""
                ],);
            }else{
                //item not bought
                return response()->json([
                    'code' => 500,
                    'msg' => "failure",
                    'data' => ""
                ],);        
            }
        }catch(\Throwable $throw){
            return response()->json([
                'code'=>500,
                'msg'=> 'The column does not exist or you have a syntax error',
                'data'=> $throw->getMessage()
           ],500);
        }

    }

    //Returning all Bought Courses detail.  Different from above.
    public function coursesBought(Request $request){
        
        //First get the user information
        $user = $request->user();               //The '$request' function makes the table that you 'request' automatically available. Remember that.
        $result =Course::join('orders', 'courses.id', '=', 'orders.course_id')   //Using Laravel Eloquent, the table name should initially be singular, then when querying, you use the actual table name whether singular or plural.
        ->select('courses.name', 'courses.thumbnail', 'courses.lesson_num', 'courses.price', 'courses.id')
        ->where('orders.status', '=', 1)
        ->where('orders.user_token', '=', $user->token)->get();     // This is how you join two tables from your database! With the conditions on how to join the tables after.
        //Select methods select the fields without returning them.            Always remeber to return the data that you selected or they won't show!!!!!! You could use the '->get()' method or whichever one like the 'show' method.
        try{
           

        
        return response()->json([
            'code' => 200,
            'msg' => 'The courses you bought are here',
            'data' => $result   //This is also Json data.
        ], 200);
        }catch(\Throwable $throw){
            return response()->json([
                'code'=>500,
                'msg'=> 'The column does not exist or you have a syntax error',
                'data'=> $throw->getMessage()
            ],500);
        }
    }
    //Returning the order list. Pretty simular to above.
    public function orderList(Request $request){
        
        //First get the user information
        $user = $request->user();               //The '$request' function makes the table that you 'request' automatically available. Remember that.
        $result =Course::join('orders', 'courses.id', '=', 'orders.course_id')   //Using Laravel Eloquent, the table name should initially be singular, then when querying, you use the actual table name whether singular or plural.
        ->select('courses.name', 'courses.thumbnail', 'courses.lesson_num', 'courses.price', 'courses.id', 'orders.status')
        //->where('orders.status', '=', 1)
        ->where('orders.user_token', '=', $user->token)->get();     // This is how you join two tables from your database! With the conditions on how to join the tables after.
        //Select methods select the fields without returning them.            Always remeber to return the data that you selected or they won't show!!!!!! You could use the '->get()' method or whichever one like the 'show' method.
        try{
           

        
        return response()->json([
            'code' => 200,
            'msg' => 'Your order list is here',
            'data' => $result   //This is also Json data.
        ], 200);
        }catch(\Throwable $throw){
            return response()->json([
                'code'=>500,
                'msg'=> 'The column does not exist or you have a syntax error',
                'data'=> $throw->getMessage()
            ],500);
        }
    }

    public function courseAuthor(Request $request){
        

        try{

            //The $result variable below is returning an associative array.
                $token = $request->token;
                $result = DB::table('admin_users')->where('token', '=', $token)
                ->select('token', 'username as name', 'avatar', 'description', 'download')->first();
                
                if(!empty($result)){
                    $result->avatar = env('APP_URL').'uploads/'.$result->avatar;     //Sharing the Admin user's backent avatar to the frontend UI.
                }     //The app on teh frontend may crash if it returns an emty value. That is why we check it first. And also why we return an empty value if null at the retrun response == 200 below.

                return response()->json([
                    'code' => 200,
                    'msg' => 'Your author info is here',
                    'data' => $result?? json_decode('{}'),   //This is also Json data.     This is saying that if there is a value you use it, otherwise we use 'json_decode('{}')' which passes an empty json object for frontend so that the app does not crash.     UNHANDLED EXCEPTION LIST<Dynamic>, not a subtype of 'Map<String, dynamic>. (You gett the gist)
                ], 200);                                      //Why aer we doing this you may ask????? It is because $result variable as explained above is an associative array, so if you pass that empty to frontend you will get an unhandled exception MEANING that you are passing an empty array or LIST (in this case the empty associative array) to frontend that is cannot use nor handle.
                                                               //In this case, to fix that, we use json_decode function to turn that empty LIST, OR ASSOCIATIVE ARRAY that the $result variable is or returns into an empty json format that it can handle in frontend. Hope this helps for future knowledge. Also, this basically means that it converts it into an PHP object. Not an associative array like the one before. So if it has a value it returns that, else it returns null values in a php object format.
            }catch(\Throwable $throw){
                return response()->json([
                    'code'=>500,
                    'msg'=> 'Something went wrong with the author info, sorry',
                    'data'=> $throw->getMessage()
                ],500);
            }

    }

    //To get the list of courses that the author created!
    public function courseListAuthor(Request $request){
        

        try{

                $token = $request->token;
                $result = Course::where('user_token', '=', $token)
                ->select('name', 'thumbnail', 'lesson_num', 'price', 'id')->get();

                return response()->json([
                    'code' => 200,
                    'msg' => 'Your authors course/lesson list info is here',
                    'data' => $result?? json_decode('{}'),
                ], 200);

            }catch(\Throwable $throw){
                return response()->json([
                    'code'=>500,
                    'msg'=> 'Something is wrong with the course List, sorry',
                    'data'=> $throw->getMessage()
                ],500);
            }

    }

}
