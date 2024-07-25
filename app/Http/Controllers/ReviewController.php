<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{

    public function store (Request $request){

        $data = $request->only(['rate', 'description', 'service_id']);
        $data['user_id'] = Auth::id();
        Review::create($data);
        return success();

    }

    public function show ($id){

        $reviews = Review::where('service_id', $id)->get();
        $averageRate = $reviews->avg('rate');
        $descriptions = $reviews->pluck('description');
        return success(null, Response::HTTP_OK, [
                'average_rate' => $averageRate,
                'descriptions' => $descriptions
            ]
        );
    }
}
