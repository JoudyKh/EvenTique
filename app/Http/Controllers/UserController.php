<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Otp\UserRegistrationOtp;
use App\Otp\UserOperationsOtp;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use SadiqSalau\LaravelOtp\Facades\Otp;
use Illuminate\Support\Facades\Notification;


class UserController extends Controller
{
    public function register (Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'min:6' , 'max:14' , 'confirmed'],
        ]);
        if(User::where('email', $request->email)->first()){
            return response([
                'message' => 'Email already exists',
                'status'=>'failed'
            ], 200);
        }

        $request->session()->put('user_email', $request->email);
        $request->session()->put('user_name', $request->name);
        $request->session()->put('user_pass', $request->password);

        $otp = Otp::identifier($request->email)->send(
            new UserRegistrationOtp(
                name: $request->name,
                email: $request->email,
                password: $request->password,
            ),
            Notification::route('mail', $request->email)
        );
        return response([
            'message' => 'Registration Success',
            'status'=>'success',
            'email' => $request->email,
            $otp['status']
        ], 201);
    }

    public function login(Request $request){
        $request->validate([
            'email'=>'required|email',
            'password'=>'required|min:6|max:14',
        ]);
        $user = User::where('email', $request->email)->first();
        if($user && Hash::check($request->password, $user->password)){
            $token = $user->createToken("Login Token")->plainTextToken;
            return response([
                'token'=>$token,
                'message' => 'Login Success',
                'status'=>'success',
                $user
            ], 200);
        }
        return response([
            'message' => 'wrong information check your email and password and do not forget to register first',
            'status'=>'failed'
        ], 401);
    }

    public function logout(Request $request){
        $request->user()->currentAccessToken()->delete();
        return response([
            'message' => 'Logout Success',
            'status'=>'success'
        ], 200);
    }

    public function logged_user(){
        $loggeduser = auth()->user();
        return response([
            'user'=>$loggeduser,
            'message' => 'Logged User Data',
            'status'=>'success'
        ], 200);
    }

    public function verRegistereOTP (Request $request){
        $request->validate([
            'code'  => ['required', 'string']
        ]);

        //$email = $request->session()->get('user_email');
        $email = $request->email;
        $userCheck = User::where('email',$email)->first();
        //return $email;
        if (!$userCheck) {
            $otp = Otp::identifier($email)->attempt($request->code);
            if ($otp['status'] != Otp::OTP_PROCESSED) {
                abort(403, __($otp['status']));
            }
            $user = User::where('email',$email)->first();
            $registerToken = $user->createToken($email)->plainTextToken;
            session()->flush();
            return response([
                'message' => 'registration verified successfully.',
                'status' => 'success',
                'rigistertoken' => $registerToken
            ], 200);
        }
        return response([
            'message' => 'registration verified failed.'
        ], 500);
    }

    public function verAuthOTP (Request $request){
        $request->validate([
            'code'  => ['required', 'string']
        ]);

        //$email = $request->session()->get('user_email');
        $email = $request->email;
        $user = User::where('email',$email)->first();
        $loggedUser = auth()->user();

        $otp = Otp::identifier($email)->attempt($request->code);
        if ($otp['status'] != Otp::OTP_PROCESSED) {
            abort(403, __($otp['status']));
        }

        if ($user) { // forget password verify

             $forgetToken = Str::random(60);
             DB::table('password_reset_tokens')->insert([
                 'email'=>$email,
                 'token'=>$forgetToken,
                 'created_at'=>Carbon::now()
             ]);
            session()->flush();
             return response([
                 'message'=>'forget password verified successfully',
                 'status'=>'success'
             ], 200);

        }else{ //change email

            $loggedUser->update([
                'email' => $email
            ]);
            session()->flush();
            return response([
                'message'=>'change email verified successfully',
                'status'=>'success'
            ], 200);
        }
    }
//    public function verRegistereOTP (Request $request){
//        $request->validate([
//            'code'  => ['required', 'string']
//        ]);
//
//        $email = $request->session()->get('user_email');
//        $user = User::where('email',$email)->first();
//
//        //user register case
//        if (!$user) {
//            $otp = Otp::identifier($email)->attempt($request->code);
//            if ($otp['status'] != Otp::OTP_PROCESSED) {
//                abort(403, __($otp['status']));
//            }
//            $user = User::where('email', $email)->first();
//            //$registerToken =
//            return response()->json([
//                'message' => 'registration verified successfully.',
//                'status' => 'success',
//                'token' => $user->createToken($email)->plainTextToken
//            ], 200);
//        }
//        //forget password case
//        else{
//            $otp = Otp::identifier($email)->attempt($request->code);
//            if ($otp['status'] != Otp::OTP_PROCESSED) {
//                abort(403, __($otp['status']));
//            }
//            $forgetToken = Str::random(60);
//            DB::table('password_reset_tokens')->insert([
//                'email'=>$email,
//                'token'=>$forgetToken,
//                'created_at'=>Carbon::now()
//            ]);
//            return response([
//                'message'=>'password verified successfully',
//                'status'=>'success'
//            ], 200);
//        }
//    }

    public function sendOTP (Request $request){
        $request->validate([
            'email'    => ['required', 'string', 'email', 'max:255']
        ]);
        $request->session()->put('user_email', $request->email);
        $name = $request->session()->get('user_name');
        $pass = $request->session()->get('user_pass');

        $usercheck = User::where('email',$request->email)->first();
        //send registration otp
        if (!$usercheck) {
            $otp = Otp::identifier($request->email)->send(
                new UserRegistrationOtp(
                    name: $name,
                    email: $request->email,
                    password: $pass,
                ),
                Notification::route('mail', $request->email)
            );
            return response([
                'message' => 'sending otp for user registration',
                'status' => 'success',
                'email' => $request->email,
                $otp['status']
            ], 200);

        }else{   // send forget password otp

            $user = USER::where('email',$request->email)->first();
            $otp = Otp::identifier($request->email)->send(
                new UserOperationsOtp(
                    name: $usercheck->name,
                    email: $user->email,
                    password: $usercheck->password,
                ),
                Notification::route('mail', $request->email)
            );
            return response([
                'message'=>'sending otp for forget password',
                'status'=>'success',
                'email' => $request->email,
                $otp['status']
            ], 200); }
    }

    public function resetPass (Request $request){
        $request->validate([
            'password' => ['required', 'min:6' , 'max:14' , 'confirmed']
        ]);
        //$email = $request->session()->get('user_email');
        $email = $request->email;
        $user = User::where('email', $email)->first();

        if(Hash::check($request->password,$user->password)) {
            return response([
                'message'=>'it is the same old password , try again',
                'status'=>'success'
            ], 500);
        }else {
            $user->update([
                'password' => Hash::make($request->password)
            ]);
            return response([
                'message'=>'updated pass successfully',
                'status'=>'success'
            ], 200);
        }
    }

    public function changePass (Request $request){
        $request->validate([
            'oldPassword' => ['required', 'min:6' , 'max:14'],
            'newPassword' => ['required', 'min:6' , 'max:14' , 'confirmed']
        ]);

        $user = auth()->user();

        if(Hash::check($request->oldPassword,$user->password)) {
            $user->update([
                'password' => Hash::make($request->newPassword)
            ]);
            return response([
                'message'=>'changed pass successfully',
                'status'=>'success'
            ], 200);
        }
            return response([
                'message'=>'your old password in wronge , try again',
                'status'=>'failed'
            ], 500);

    }
    public function changeEmailOTP(Request $request){
        $request->validate([
            'password' => ['required', 'min:6' , 'max:14'],
            'email'    => ['required', 'string', 'email', 'max:255']
        ]);

        $userAuth = auth()->user();
        if(Hash::check($request->password, $userAuth->password)){

            $user = User::where('email', $request->email)->first();
            if ($user) {
                return response([
                    'message' => 'sorry , email dosent available'
                ], 500);
            }
            $request->session()->put('user_email', $request->email);
            $otp = Otp::identifier($request->email)->send(
                new UserOperationsOtp(
                    name: $userAuth->name,
                    email: $request->email,
                    password: $userAuth->password,
                ),
                Notification::route('mail', $request->email)
            );
            return response([
                'message' => 'sending otp for change email',
                'status' => 'success',
                $otp['status']
            ], 200);
    }
        return response([
            'message' => 'sorry , your password is wrong , please try again',
            'status' => 'failed',
        ], 500);
    }
}
