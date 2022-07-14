<?php

namespace App\Jobs;

use App\Models\Wallet;
use App\Models\Withdrawal;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class WithdrawJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The withdrawal instance.
     *
     * @var \App\Models\Withdrawal
     */
    public $withdrawal;
    /**
     * The wallet instance.
     *
     * @var \App\Models\Wallet
     */
    public $wallet;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Withdrawal $withdrawal, Wallet $wallet)
    {
        $this->withdrawal = $withdrawal;
        $this->wallet = $wallet;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if($this->wallet->balance > $this->withdrawal->amount) {
            $this->wallet->balance = $this->wallet->balance - $this->withdrawal->amount;
            $this->wallet->save();

            $this->withdrawal->status = 'success';
            $this->withdrawal->save();
        } else {
            $this->withdrawal->status = 'failed';
            $this->withdrawal->save();
        }
    }
}
