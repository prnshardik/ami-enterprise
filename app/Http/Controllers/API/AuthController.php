<?php

    namespace App\Http\Controllers\API;

    use App\Http\Controllers\Controller;
    use Illuminate\Http\Request;
    use App\Models\User;
    use Auth, DB, Validator, File;

    class AuthController extends Controller{
        
        /** login */
            public function login(Request $request){
                $rules = ['email' => 'required', 'password' => 'required'];

                $validator = Validator::make($request->all(), $rules);

                if($validator->fails())
                    return response()->json(['status' => 422, 'message' => $validator->errors()]);
                
                $auth = auth()->attempt(['email' => $request->email, 'password' => $request->password]);

                if(!$auth){
                    return response()->json(['status' => 401, 'message' => 'Invalid login details']);
                }else{
                    $user = User::where('email', $request->email)->firstOrFail();

                    if($user->status == 'active'){
                        $token = $user->createToken('auth_token')->plainTextToken;

                        return response()->json(['status' => 200, 'message' => 'Login Successfully', 'token_type' => 'Bearer', 'access_token' => $token]);
                    }else{
                        return response()->json(['status' => 201, 'message' => 'This account has been inactive or deleted, please contact admin']);
                    }
                }
            }
        /** login */

        /** logout */
            public function logout(Request $request){
                $request->user()->currentAccessToken()->delete();

                return response()->json(['status' => 200, 'message' => 'Logout Successfully']);
            }
        /** logout */
    }
