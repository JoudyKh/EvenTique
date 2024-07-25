<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FavoriteController extends Controller
{
    public function store(Request $request){
        $user = auth()->user();
        Favorite::create([
            'user_id' => $user->id,
            'service_id' => $request->service_id
        ]);
        return success();
    }

    public function destroy($id){
        $user = auth()->user();
        $fav = Favorite::where('user_id', $user->id)
            ->where('service_id', $id)
            ->first();
        $fav->delete();
        return success();
    }

    public function index(){
        $user = auth()->user();
        if($user){
            return success($user->favorites);
        }
        return error('Invalid Auth');
    }
}
