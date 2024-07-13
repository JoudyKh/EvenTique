<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Favorite;
use App\Models\User;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    //$category->drugs()->attach($drugId);
    public function addCategory(Request $request){
        //$user = auth()->user();
        Category::create([
            'name' => $request->name
        ]);
        return response([
            'message' => 'insert Success'
        ], 200);
    }

    public function deleteCategory (Request $request){
        //$user = auth()->user();
        $cat = Category::where('name', $request->name)->first();
        $cat->delete();
        return response([
            'message' => 'delete Success'
        ], 200);
    }

    public function showCategory(){
        //$user = auth()->user();
        $cat = Category::all();
        return response([
                'message' => 'Success',
                $cat
            ], 200);
    }
    public function catServices(Request $request){
        $cat = Category::where('name', $request->name)->first();
        return response([
            'message' => 'Success',
            $cat->services
        ], 200);
    }
}
