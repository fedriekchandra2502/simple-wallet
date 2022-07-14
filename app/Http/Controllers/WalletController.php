<?php

namespace App\Http\Controllers;

use App\Jobs\DepositJob;
use App\Jobs\WithdrawJob;
use App\Models\Deposit;
use App\Models\Wallet;
use App\Models\Withdrawal;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

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
                ->select(['id','owned_by','status','enabled_at','balance'])->first();
        
        if($wallet->status == 'disabled') {
            $resp = apiResp(['error' => 'this wallet is disabled'],'fail');
            return response()->json($resp, 400);
        }

        $resp = apiResp(compact('wallet'));
        return response()->json($resp);
    }

    public function deposit()
    {
        $data = request()->validate([
            'amount' => ['required','integer'],
            'reference_id' => ['required', Rule::unique('deposits','reference_id')]
        ]);
        $user = auth()->user();
        $wallet = $user->wallet;

        if($wallet->status == 'disabled') {
            $resp = apiResp(['error' => 'this wallet is disabled'],'fail');
            return response()->json($resp, 400);
        }

        $deposit = Deposit::create([
            'deposited_by' => $user->id,
            'status' => 'pending',
            'amount' => $data['amount'],
            'reference_id' => $data['reference_id'],
            'deposited_at' => Carbon::now()
        ]);

        dispatch(new DepositJob($deposit, $wallet))->delay(now()->addSeconds(10));
        
        $resp = apiResp(compact('deposit'));
        return response()->json($resp);
    }

    public function withdraw()
    {
        $data = request()->validate([
            'amount' => ['required','integer'],
            'reference_id' => ['required', Rule::unique('withdrawals','reference_id')]
        ]);

        $user = auth()->user();
        $wallet = $user->wallet;

        if($wallet->status == 'disabled') {
            $resp = apiResp(['error' => 'this wallet is disabled'],'fail');
            return response()->json($resp, 400);
        }

        if($wallet->balance < $data['amount']) {
            $resp = apiResp(['error' => 'withdrawal amount exceeding wallet balance'], 'fail');
            return response()->json($resp, 400);
        }

        $withdraw = Withdrawal::create([
            'withdrawn_by' => $user->id,
            'status' => 'pending',
            'amount' => $data['amount'],
            'reference_id' => $data['reference_id'],
            'withdrawn_at' => Carbon::now()
        ]);

        dispatch(new WithdrawJob($withdraw, $wallet))->delay(now()->addSeconds(5));

        $resp = apiResp(compact('withdraw'));
        return response()->json($resp);
    }

    public function disableWallet()
    {
        $data = request()->validate([
            'is_disabled' => ['required','boolean']
        ]);

        $user = auth()->user();
        $wallet = $user->wallet;

        if($data['is_disabled']) {
            $wallet->status = 'disabled';
            $wallet->disabled_at = Carbon::now();
            $wallet->save();
        }

        $resp = apiResp(compact('wallet'));
        return response()->json($resp);
    }
}
