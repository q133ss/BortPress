<?php

namespace App\Services\Subscribe;

use App\Jobs\CheckPayJob;
use App\Jobs\LastCheckPayJob;
use App\Models\Payment;
use App\Models\PaymentBuffer;
use App\Models\User;
use Carbon\Carbon;

class SubscribeService
{
    /**
     * Проверяет всех юзеров и возвращает неоплаченных
     * @return array
     */
    public function checkAll(): array
    {
        $users = User::all();
        $not_paid = [];
        foreach ($users as $user){
            if(now()->greaterThan(Carbon::parse($user->subscribe_end))){
                $not_paid[] = $user->id;
            }
        }

        return $not_paid;
    }

    private function pay(int $user_id, float $sum, string $description){
        #TODO тут попытка оплаты
        $payment = ['id' => 222];
        $pay = Payment::create([
            'user_id'     => $user_id,
            'sum'         => $sum,
            'description' => $description,
            'status'      => 'wait',
            'pay_id'      => $payment['id']
        ]);
        return $pay;
    }

    public function attemptPay(array $user_ids): true
    {
        foreach ($user_ids as $id){
            $payment = Payment::where('user_id', $id);
            if($payment->exists()){
                $payment->pluck('pay_id')->latest()->first();
                #TODO тут делаем попытку оплаты!
                $pay = $this->pay($id, 100.0, 'Оплата подписки');

                PaymentBuffer::create([
                    'pay_id' => $pay->id,
                    'user_id' => $id
                ]);
            }
        }

        return true;
    }

    public function checkStatus(string $pay_id){
        $statuses = [true, false];
        return $statuses[rand(0,1)];
    }

    #TODO проверить!
    public function checkPay(): true
    {
        $buffers = PaymentBuffer::get();
        for($i = 1; $i <= 10; $i++){
            CheckPayJob::dispatch($buffers)->delay(now()->addMinutes($i));
        }
        LastCheckPayJob::dispatch($buffers)->delay(11);
        return true;
    }
}
