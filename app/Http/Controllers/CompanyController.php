<?php

namespace App\Http\Controllers;

use App\Http\Requests\Company\CompanyRequest;
use App\Http\Resources\CompanyResource;
use App\Models\Application;
use App\Models\Company;
use App\Models\CompanyWallet;
use App\Models\User;
use App\Models\WorkHours;
use App\Otp\UserOperationsOtp;
use App\Otp\UserRegistrationOtp;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use SadiqSalau\LaravelOtp\Facades\Otp;
use Stichoza\GoogleTranslate\GoogleTranslate;


class CompanyController extends Controller
{
    public function insertcompany(CompanyRequest $request)
    {
        $validatedData = $request->validated();

        $currentLocale = app()->getLocale();
        $targetLocale = ($currentLocale === 'en') ? 'ar' : 'en';

        $lang = new GoogleTranslate($currentLocale);
        $lang->setSource($currentLocale)->setTarget($targetLocale);

        $validatedData['location'] = [
            $currentLocale => $validatedData['location'],
            $targetLocale => $lang->translate($validatedData['location'])
        ];
        $validatedData['city'] = [
            $currentLocale => $validatedData['city'],
            $targetLocale => $lang->translate($validatedData['city'])
        ];
        $validatedData['country'] = [
            $currentLocale => $validatedData['country'],
            $targetLocale => $lang->translate($validatedData['country'])
        ];
        $validatedData['description'] = [
            $currentLocale => $validatedData['description'],
            $targetLocale => $lang->translate($validatedData['description'])
        ];

        $validatedData['password'] = Hash::make($validatedData['password']);
        $company = Company::create($validatedData);
        $company->images()->create(['url' => $request->image]);
        CompanyWallet::create([
            'company_id'=>$company->id
        ]);
        Application::create([
            'company_id'=>$company->id
        ]);
        $registerToken = $company->createToken($company->email)->plainTextToken;
        $firebaseToken = $company->createCustomToken($company->id);

        foreach ($validatedData['work_hours'] as $workHour) {
            $workHour['day'] = [
                $currentLocale => $workHour['day'],
                $targetLocale => $lang->translate($workHour['day'])
            ];
            WorkHours::create([
                'day' => json_encode($workHour['day']),
                'hours_from' => $workHour['hours_from'],
                'hours_to' => $workHour['hours_to'],
                'company_id' => $company->id
            ]);
        }

        foreach ($request->category_id as $categoryID) {
            $company->categories()->attach($categoryID);
        }
        foreach ($request->event_type_id as $eventTypeID) {
            $company->eventTypes()->attach($eventTypeID);
        }

        return success(new CompanyResource($company), Response::HTTP_OK, [
            'image' => $company->images(),
            'workHours' => $company->workHours,
            'registerToken' => $registerToken,
            'firebaseToken' => $firebaseToken
        ]);
    }
    public function destroy(Request $request)
    {
        $company = auth()->user();
        $company->delete();
        return success();
    }

    public function show($id)
    {
        $company = Company::find($id);
        return success(new CompanyResource($company), Response::HTTP_OK, [
            'image' => $company->images(),
            'workHours' => $company->workHours,
        ]);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6|max:14',
        ]);
        $company = Company::where('email', $request->email)->first();
        if ($company && Hash::check($request->password, $company->password)) {

            return success(new CompanyResource($company), Response::HTTP_OK, [
                'loggintoken' => $company->createToken("Comp Login Token")->plainTextToken
            ]);
        }
        return error('wrong information check your email and password');
    }

    public function logout(){
        $company = auth()->user();
        $company->currentAccessToken()->delete();
        return success();
    }

    public function index(){
        return CompanyResource::collection(Company::all());
    }

    public function sendOTP (Request $request){
        $request->validate([
            'email'    => ['required', 'string', 'email', 'max:255']
        ]);
          // send forget password otp
        $company = Company::where('email', $request->email)->first();
        if($company) {
            $otp = Otp::identifier($request->email)->send(
                new UserOperationsOtp(
                    name: $company->company_name,
                    email: $company->email,
                    password: $company->password,
                ),
                Notification::route('mail', $request->email)
            );
            return success($otp['status']);
        }
        return error('check your email');
    }


    public function verAuthOTP (Request $request){
        $request->validate([
            'code'  => ['required', 'string']
        ]);
        $company = Company::where('email',$request->email)->first();

        $otp = Otp::identifier($request->email)->attempt($request->code);
        if ($otp['status'] != Otp::OTP_PROCESSED) {
            abort(403, __($otp['status']));
        }

        if ($company) { // forget password verify
            DB::table('password_reset_tokens')->insert([
                'email'=>$request->email,
                'token'=>$forgetToken = Str::random(60),
                'created_at'=>Carbon::now()
            ]);
            return success($otp['status']);
        }
    }

    public function resetPass (Request $request){
        $request->validate([
            'password' => ['required', 'min:6' , 'max:14' , 'confirmed']
        ]);

        $company = Company::where('email', $request->email)->first();

        if(Hash::check($request->password,$company->password)) {
            return error('it is the same old password , try again');
        }else {
            $company->update([
                'password' => Hash::make($request->password)
            ]);
            return success();
        }
    }

}
