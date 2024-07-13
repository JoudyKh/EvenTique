<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FavoriteController extends Controller
{
    public function addFavorite(Request $request){
        $user = auth()->user();
        Favorite::create([
            'user_id' => $user->id,
            'service_id' => $request->service_id
        ]);
        return response([
            'message' => 'insert Success'
        ], 200);
    }

    public function deleteFavorite(Request $request){
        $user = auth()->user();
        $fav = Favorite::where('user_id', $user->id)
            ->where('service_id', $request->service_id)
            ->first();
        $fav->delete();
        return response([
            'message' => 'delete Success'
        ], 200);
    }

    public function showfavorite(){
        $user = auth()->user();
        $show =User::find($user->id);
        if($show){
            return response([
                'message' => 'Success',
                $show->favorites
            ], 200);
        }
        return response([
            'message' => 'failed'
        ], 500);
    }
}
