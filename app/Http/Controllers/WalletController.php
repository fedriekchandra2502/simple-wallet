<?php

namespace App\Http\Controllers;

use App\Models\Wallet;
use Carbon\Carbon;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    public function enableWallet()
    {
        $user = auth()->user();
        $wallet = $user->wallet;

        if($wallet) {

            if($wallet->status == 'enabled') {

                $resp = apiResp(['status' => 'wallet already has been enabled'],'fail');

                return response()->json($resp,400);

            } else {

                $wallet->status = 'enabled';
                $wallet->save();

            }
           
        } else {
            $wallet = $user->wallet()->create([
                'status' => 'enabled',
                'balance' => 0,
                'enabled_at' => Carbon::now()
            ]);
        }
        
        $resp = apiResp(compact('wallet'));

        return response()->json($resp);
    }

    public function show()
    {
        $user = auth()->user();
        $wallet = $user->wallet()
                ->select(['id','owned_by','status','enabled_at','balance'])->get();

        $resp = apiResp(compact('wallet'));
        return response()->json($resp);
    }
}
