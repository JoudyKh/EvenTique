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
use Illuminate\Http\Response;

class AdminController extends Controller
{
    public function admin(Request $request){
        $admin = Admin::create([
            'email'=>$request->email,
            'password'=>Hash::make($request->password)
        ]);
        $registerToken = $admin->createToken($admin->email)->plainTextToken;
        return $registerToken;
    }
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6|max:14',
        ]);
        $admin = Admin::where('email', $request->email)->first();
        if ($admin && Hash::check($request->password, $admin->password)) {

            return success(null, Response::HTTP_OK, [
                'loggintoken' => $admin->createToken("admin Login Token")->plainTextToken
            ]);
        }
        return error('wrong information check your email and password');
    }

    public function logout(){
        $admin = auth()->user();
        $admin->currentAccessToken()->delete();
        return success();
    }

    public function verAuthOTP (Request $request){
        $request->validate([
            'code'  => ['required', 'string']
        ]);
        $admin = Admin::first();

        $otp = Otp::identifier($admin->email)->attempt($request->code);
        if ($otp['status'] != Otp::OTP_PROCESSED) {
            abort(403, __($otp['status']));
        }

        if ($admin) { // forget password verify
            DB::table('password_reset_tokens')->insert([
                'email'=>$admin->email,
                'token'=>$forgetToken = Str::random(60),
                'created_at'=>Carbon::now()
            ]);
            return success($otp['status']);
        }
    }

    public function sendOTP (){
        // send forget password otp
        $admin = Admin::first();
        if($admin) {
            $otp = Otp::identifier($admin->email)->send(
                new UserOperationsOtp(
                    name: $admin->name,
                    email: $admin->email,
                    password: $admin->password,
                ),
                Notification::route('mail', $admin->email)
            );
            return success($otp['status']);
        }
        return error('check your email');
    }


    public function resetPass (Request $request){
        $request->validate([
            'password' => ['required', 'min:6' , 'max:14' , 'confirmed']
        ]);

        $admin = Admin::first();
        if(Hash::check($admin->password,$admin->password)) {
            return error('it is the same old password , try again');
        }else {
            $admin->update([
                'password' => Hash::make($request->password)
            ]);
            return success();
        }
    }
}
