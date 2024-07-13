<?php

namespace App\Http\Controllers;
use App\Models\Service;
use Illuminate\Http\Request;


class ServiceController extends Controller
{
    public function addService(Request $request){
        //$user = auth()->user();
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'price' => ['required', 'digits:10', 'numeric'],
            'discription' => ['required', 'string', 'max:255'],
            'companies_id' => ['required', 'string', 'max:255'],
            'categories_id' => ['required', 'string', 'max:255'],
        ]);
        Service::create([
            'name' => $request->name,
            'price' => $request->price,
            'discription' => $request->discription,
            'discounted_packages' => $request->discounted_packages,
            'activation' => $request->activation,
            'categories_id' => $request->categories_id,
            'companies_id' => $request->companies_id
        ]);
        return response([
            'message' => 'insert Success'
        ], 200);
    }

    public function deleteService (Request $request){
        //$user = auth()->user();
        $ser = Service::where('name', $request->name)->first();
        $ser->delete();
        return response([
            'message' => 'delete Success'
        ], 200);
    }

    public function showService($id){
        //$user = auth()->user();
        $ser = Service::where('id', $id)->first();
        return response([
            'message' => 'Success',
            $ser
        ], 200);
    }
}
