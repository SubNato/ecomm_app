<?php

namespace App\Http\Controllers\Teacher;

use App\Models\AdminUser;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class TeacherController extends Controller
{
    //
    public function login(Request $request){
        try{
            //Validated
            $validateUser = Validator::make($request->all(),
            [
                'username' => 'required',
                'password' => 'required'

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
            $map['username'] = $validated['username'];
            
            $user = AdminUser::where($map)->first();

            if(empty($user->id)){
                return response()->json(['code'=>400, 'data'=>'', 'msg'=>'User does not exist'], 400);
            }

            if(!Hash::check($validated['password'], $user->password)){
                return response()->json(['code'=>403, 'data'=>'', 'msg'=>'You are not authorized'], 403);
            }
            
            $accessToken = $user->createToken(uniqid())->plainTextToken;
            $user->access_token = $accessToken;

            return response()->json(['code'=>200, 'data'=>$user, 'msg'=>'User found!'], 200);


        }catch (\Throwable $th) {                       //Remember that problems may arise from the database as well (its structure, like something is not null that should be have a null type field like explained above at avatar)
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }
}
