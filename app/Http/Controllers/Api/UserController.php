<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    //
    /**
     * Create User
     * @param Request $request
     * @return User
     */
    public function createUser(Request $request)
    {

        try {
            //Validated
            $validateUser = Validator::make($request->all(),
            [
                'avatar' => 'required',       //If it is required make sure it is not null, if it is null you will get an error from backend. 
                'type' => 'required',            //Why? Because it is required, and because it is not entered it causes a problem (due to it always being null).
                'open_id' => 'required',
                'name' => 'required',
                'email' => 'required',
                //'password' => 'required|min:6'

            ]);
            
            if($validateUser->fails()){
                return response()->json([
                    'status' => false,
                    'message' => 'validation error',
                    'errors' => $validateUser->errors()
                ], 401);
            }
            //validated will have all user field values
            //we can save in the database
            $validated = $validateUser->validated();

            $map=[];
            $map['type'] = $validated['type'];     //Type being like email,phone,google,facebook,apple
            $map['open_id'] = $validated['open_id'];

            $user = User::where($map)->first();     //Returning every user column in the database.
            
            //Check if the user has logged in or not.
            //Empty means that the user does not exist
            //then save the user in the database for the first time
            if(empty($user->id)){

                //This user has never been in the database.
                //So assign the user in the database.
                //This token is the user id.
                $validated["token"] = md5(uniqid().rand(10000,99999));
                //user first time created
                $validated['created_at'] = Carbon::now();
                
                //encrypt password
              //$validated['password'] = Hash::make($validated['password']);
                //returns the ID of the row after saving.
                $userID = User::insertGetId($validated);
                //All the user's information
                $userInfo = User::where('id', '=', $userID)->first();

                $accessToken = $userInfo->createToken(uniqid())->plainTextToken;

                $userInfo->access_token = $accessToken;
                User::where('id', '=', $userID)->update(['access_token'=>$accessToken]);
                return response()->json([
                    'code' => 200,
                    'msg' => 'User Created Successfully',
                    'data' => $userInfo
                ], 200);

            }
                    //Each time a user logs in, they need a token. Here is after/where the user has logged in.
            $accessToken = $user->createToken(uniqid())->plainTextToken;      //If the variable was used in a bloc (if statement) like above, you can't use it outside of the bloc (like $userID, or id etc.)
            $user->access_token = $accessToken;
            User::where('open_id', '=', $validated['open_id'])->update(['access_token'=>$accessToken]);
            return response()->json([
                'code' => 200,
                'msg' => 'User logged in Successfully',
                'data' => $user   //This is also Json data.
            ], 200);         //This json array is actually called an associative array!  Also, you can look at it as nested Json data. As it is Json data in Json data( the one that returns the data to the server for frontend use)!
                                //Note that it has to be the same on the Frontend as well for it to work! As what is done in our app's case. (Note 'return data' in 'http_util'.dart file), or (Note 'UserLoginReponseEntity' in 'user.dart' file which literally maps these fields, then passes them as data through the calling of the different functions).
        }catch (\Throwable $th) {                       //Remember that problems may arise from the database as well (its structure, like something is not null that should be have a null type field like explained above at avatar)
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Login The User
     * @param Request $request
     * @return User
     */
    public function loginUser(Request $request)
    {
        try {
            $validateUser = Validator::make($request->all(),
            [
                'email' => 'required|email',
                'password' => 'required'
            ]);

            if($validateUser->fails()){
                return response()->json([
                    'status' => false,
                    'message' => 'validation error',
                    'errors' => $validateUser->errors()
                ], 401);
            }

            if(!Auth::attempt($request->only(['email', 'password']))){ /** Says if they have registered or not */
                return response()->json([
                    'status' => false,
                    'message' => 'Email & Password do not match with our records.',
                ], 401);
            }

            $user = User::where('email', $request->email)->first();

            return response()->json([
                'status' => true,
                'message' => 'User Logged In Successfully',
                'token' => $user->createToken("API TOKEN")->plainTextToken
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'status' =>false,
                'message' =>$th->getMessage()
            ], 500);
        }
    }


    /*                  //Great to use this for debugging purposes!
    return response()->json([
        'status'=>true,
        'data'=>$validated,
        'message' => 'passed validation'
    ], 200);
    */
}
