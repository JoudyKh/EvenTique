<?php

namespace App\Http\Controllers;

use App\Models\EventType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EventController extends Controller
{

    public function insertEventType(Request $request){
        $request->validate([
            'name' => ['required', 'string', 'max:255']
            ]);
        $event = EventType::where('name',$request->name)->first();
        if($event){
            return response([
                'message' => 'the Event is booked'
            ], 500);
        }
        EventType::create([
            'name'=>$request->name
        ]);
        return response([
            'message' => 'insert Success'
        ], 200);
    }

    public function deleteEventType(Request $request){
        $event = EventType::where('name',$request->name)->first();
        if($event) {
            $event->delete();
            return response([
                'message' => 'delete Success'
            ], 200);
        }
        return response([
            'message' => 'deleted failed , try again'
        ], 500);
    }

    public function showAllEventType(){
        $show =DB::table('event_types')->get();
        if($show){
            return response([
                'message' => 'Success',
                $show
            ], 200);
        }
        return response([
            'message' => 'failed'
        ], 500);
    }

    public function addEvent (Request $request){
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'date' => ['required', 'string', 'max:255'],
            'time' => ['required', 'string', 'max:255'],
            'pudget' => ['required', 'string', 'max:255'],
            'guests' => ['required', 'string', 'max:255'],
            'event_type_id' => ['required', 'string', 'max:255'],
            'user_id' => ['required', 'min:6' , 'max:14' , 'confirmed'],
        ]);
    }
}
