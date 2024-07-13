<?php

namespace App\Http\Controllers;

use App\Http\Resources\CompanyResource;
use App\Models\Company;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class CompanyController extends Controller
{
    public function addCompany(Request $request)
    {
        //$user = auth()->user();
        $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:companies,email'],
            'password' => ['required', 'min:6', 'max:14', 'confirmed'],
            'phone_number' => ['required', 'digits:10', 'numeric'],
            'company_name' => ['required', 'string', 'max:255'],
            'registration_number' => ['required', 'string', 'max:255'],
            // 'location' => ['required', 'string', 'max:255'],
            'ar_location' => 'required',
            'en_location' => 'required',
            'ar_city' => 'required',
            'en_city' => 'required',
            'ar_country' => 'required',
            'en_country' => 'required',
            'ar_description' => 'required',
            'en_description' => 'required',
            
            'city' => ['required', 'string', 'max:255'],
            'country' => ['required', 'string', 'max:255'],
            //'days' => ,
            //'hours_from' => ,
            //'hours_to' => ,
            'description' => ['required', 'string', 'max:255'],
            //'accept_privacy' => ,
            'event_type_id' => ['required']
        ]);
        
        // منعمل كلشي ركوستات ب form request
        // بعد مانعملهم بفورم ركوست منكتب
        // $data = $request->validated();
        // بهي التعليمة رح خزن بقلب الداتا كلشي عواميد انعمللها فاليديت متل فوق

        // هون حلقة بتمر على عواميد الترجمة بس
        foreach(Company::$trsnalatableAtt as $item){
            foreach (config('app.locales') as $locale) {
                $data[$item][$locale] = $request->{$locale . '_' . $item};
            }}
            $company = Company::create($data);
            return new CompanyResource($company);







        // Company::create([
        //     'first_name' => $request->first_name,
        //     'last_name' => $request->last_name,
        //     'email' => $request->email,
        //     'password' => $request->password,
        //     'phone_number' => $request->phone_number,
        //     'company_name' => $request->company_name,
        //     'registration_number' => $request->registartion_number,
        //     'location' => $request->location,
        //     'city' => $request->city,
        //     'country' => $request->country,
        //     'days' => $request->days,
        //     'hours_from' => $request->hours_from,
        //     'hours_to' => $request->hours_to,
        //     'description' => $request->description,
        //     'accept_privacy' => $request->accept_privacy,
        //     'event_type_id' => $request->event_type_id
        // ]);
        // return response([
        //     'message' => 'insert Success'
        // ], 200);
    }

    public function deleteCompany(Request $request)
    {
        //$user = auth()->user();
        $com = Company::where('company_name', $request->company_name)->first();
        $com->delete();
        return response([
            'message' => 'delete Success'
        ], 200);
    }

    public function showCompany(Request $request)
    {
        //$user = auth()->user();
        $com = Company::where('company_name', $request->company_name)->first();
        return response([
            'message' => 'Success',
            $com
        ], 200);
    }


//company ui
//////////////////////////////////////////////////////////////////////////////////

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6|max:14',
        ]);
        $comp = Company::where('email', $request->email)->first();
        if ($comp && Hash::check($request->password, $comp->password)) {
            $token = $comp->createToken("Comp Login Token")->plainTextToken;
            return response([
                'token' => $token,
                'message' => 'Login Success',
                'status' => 'success',
                $comp
            ], 200);
        }
        return response([
            'message' => 'wrong information check your email and password and do not forget to register first',
            'status' => 'failed'
        ], 401);
    }

}
