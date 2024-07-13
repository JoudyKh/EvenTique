<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\User;
use App\Otp\UserOperationsOtp;
use App\Otp\UserRegistrationOtp;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use SadiqSalau\LaravelOtp\Facades\Otp;

class AdminController extends Controller
{
    public function login(Request $request){
        $request->validate([
            'email'=>'required|email',
            'password'=>'required|min:6|max:14',
        ]);
        $admin = Admin::find(1);
        if($admin && Hash::check($request->password, $admin->password)){
            $token = $admin->createToken("Admin Login Token")->plainTextToken;
            return response([
                'token'=>$token,
                'message' => 'Login Success',
                'status'=>'success',
                $admin
            ], 200);
        }
        return response([
            'message' => 'wrong information check your email and password and do not forget to register first',
            'status'=>'failed'
        ], 401);
    }

    public function verOTP (Request $request){
        $request->validate([
            'code'  => ['required', 'string']
        ]);

        $email = $request->email;
        $admin = Admin::where('email',$email)->first();
        $loggedUser = auth()->user();

        $otp = Otp::identifier($email)->attempt($request->code);
        if ($otp['status'] != Otp::OTP_PROCESSED) {
            abort(403, __($otp['status']));
        }

        if ($admin) { // forget password verify

            $forgetToken = Str::random(60);
            DB::table('password_reset_tokens')->insert([
                'email'=>$email,
                'token'=>$forgetToken,
                'created_at'=>Carbon::now()
            ]);
            return response([
                'message'=>'forget password verified successfully',
                'status'=>'success'
            ], 200);

        }else{ //change email

            $loggedUser->update([
                'email' => $email ]);
            return response([
                'message'=>'change email verified successfully',
                'status'=>'success'
            ], 200);
        }
    }

    public function sendOTP (Request $request){
        $request->validate([
            'email'    => ['required', 'string', 'email', 'max:255']
        ]);
            $admin = Admin::find(1);
            $otp = Otp::identifier($request->email)->send(
                new UserOperationsOtp(
                    name: $admin->name,
                    email: $request->email,
                    password: $admin->password,
                ),
                Notification::route('mail', $request->email)
            );
            return response([
                'message'=>'sending otp for forget password',
                'status'=>'success',
                'email' => $request->email,
                $otp['status']
            ], 200); }


    public function resetPass (Request $request){
        $request->validate([
            'password' => ['required', 'min:6' , 'max:14' , 'confirmed']
        ]);
        $email = $request->email;
        $admin = Admin::where('email', $email)->first();
        if(Hash::check($request->password,$admin->password)) {
            return response([
                'message'=>'it is the same old password , try again',
                'status'=>'success'
            ], 500);
        }else {
            $admin->update([
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

        $admin = auth()->user();
        if(Hash::check($request->oldPassword,$admin->password)) {
            $admin->update([
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

        $AdminAuth = auth()->user();
        if(Hash::check($request->password, $AdminAuth->password)){

            $admin = User::where('email', $request->email)->first();
            if ($admin) {
                return response([
                    'message' => 'sorry , email dosent available'
                ], 500);
            }
            $otp = Otp::identifier($request->email)->send(
                new UserOperationsOtp(
                    name: $AdminAuth->name,
                    email: $request->email,
                    password: $AdminAuth->password,
                ),
                Notification::route('mail', $request->email)
            );
            return response([
                'message' => 'sending otp for change email',
                'status' => 'success',
                'new email' => $request->email,
                $otp['status']
            ], 200);
        }
        return response([
            'message' => 'sorry , your password is wrong , please try again',
            'status' => 'failed',
        ], 500);
    }

    public function admin(){
        $admin = Admin::find(1);
        return $admin->email;
    }
}
