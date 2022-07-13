<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function init()
    {
        $data = request()->validate([
            'customer_xid' => ['required']
        ]);

        $user = User::query()->firstOrCreate([
            'id' => $data['customer_xid']
        ]);

        $token = $user->createToken($user->id)->plainTextToken;
        $data = compact('token');
        $resp = apiResp($data);

        return response()->json($resp);
    }
}
