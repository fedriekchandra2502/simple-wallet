<?php

namespace App\Http\Controllers;

use App\Models\Deposit;
use App\Models\Wallet;
use App\Models\Withdrawal;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
                ->select(['id','owned_by','status','enabled_at','balance'])->get();

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
        $deposit = Deposit::create([
            'deposited_by' => $user->id,
            'status' => 'success',
            'amount' => $data['amount'],
            'reference_id' => $data['reference_id'],
            'deposited_at' => Carbon::now()
        ]);
        
        DB::beginTransaction();
        try {
            $wallet = $user->wallet;
            $wallet->balance = $wallet->balance + $data['amount'];
            $wallet->save();
            DB::commit();
        } catch(\Exception $e) {
            DB::rollBack();
            $deposit->status = 'failed';
            $deposit->save();
        }
        
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

        $withdrawalStatus = 'success';

        if($wallet->balance < $data['amount']) {
            $withdrawalStatus = 'failed';
        }

        $withdraw = Withdrawal::create([
            'withdrawn_by' => $user->id,
            'status' => $withdrawalStatus,
            'amount' => $data['amount'],
            'reference_id' => $data['reference_id'],
            'withdrawn_at' => Carbon::now()
        ]);

        DB::beginTransaction();
        try {
            $wallet->balance = $wallet->balance - $data['amount'];
            $wallet->save();
            DB::commit();
        } catch(\Exception $e) {
            DB::rollBack();
            $withdraw->status = 'failed';
            $withdraw->save();
        }

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
