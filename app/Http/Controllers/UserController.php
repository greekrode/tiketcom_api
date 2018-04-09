<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;

class UserController extends Controller
{
    public function store(Request $request, $id){
        $user = User::find($id);
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->phone_number = $request->phone_number;
        $user->DOB = $request->DOB;
        $user->nationality = $request->nationality;

        $user->save();

        return response()->json(['success'=>'True']);
    }

    public function show($id)
    {
        $user = User::find($id);
        if ($user == null) {
          echo "No data with the specified description";
        }
        else {
        return response()->json($user->toArray());
        }
    }
}
