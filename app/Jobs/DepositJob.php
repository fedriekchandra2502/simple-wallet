<?php

namespace App\Jobs;

use App\Models\Deposit;
use App\Models\Wallet;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class DepositJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The deposit instance.
     *
     * @var \App\Models\Deposit
     */
    public $deposit;
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
    public function __construct(Deposit $deposit, Wallet $wallet)
    {
        $this->deposit = $deposit->withoutRelations();
        $this->wallet = $wallet->withoutRelations();
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->wallet->balance = $this->wallet->balance + $this->deposit->amount;
        $this->wallet->save();

        $this->deposit->status = 'success';
        $this->deposit->save();
    }
}
