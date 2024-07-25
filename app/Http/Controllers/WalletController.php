<?php

namespace App\Http\Controllers;

use App\Models\UserWallet;
use Illuminate\Http\Request;

class WalletController extends Controller
{

    public function store(Request $request){
        $request->validate([
            'amount' => ['required' , 'numeric']
        ]);
        $user = auth()->user();
        $wallet = UserWallet::where('user_id',$user->id)->first();
        if($request->amount > 0) {
            $wallet->update([
                'amount' => $wallet->amount + $request->amount
            ]);
            return success($wallet->amount);
        }
        return error('amount should be bigger than 0');
    }

    public function index(){
        $user = auth()->user();
        if($user){

            return success($user->userwallets->amount);
        }
        return error('Invalid Auth');
    }
}
