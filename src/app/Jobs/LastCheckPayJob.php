<?php

namespace App\Jobs;

use App\Models\Payment;
use App\Models\PaymentBuffer;
use App\Services\Subscribe\SubscribeService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class LastCheckPayJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $buffers;

    /**
     * Create a new job instance.
     */
    public function __construct($buffers)
    {
        $this->buffers = $buffers;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $buffers = $this->buffers;

        Log::build([
            'driver' => 'single',
            'path' => storage_path('logs/payment.log'),
        ])->info('Начинаю последнюю проверку платежей: {payments}', ['payments' => $buffers]);

        $service = new SubscribeService();
        foreach ($buffers as $buffer){
            if(PaymentBuffer::where('id', $buffer->id)->exists()){
                $status = $service->checkStatus($buffer->pay_id);
                if($status){
                    $payment = Payment::find($buffer->pay_id);
                    if($payment != null) {
                        $payment->update(['status' => 'done']);
                    }
                }else{
                    $payment = Payment::find($buffer->pay_id);
                    if($payment != null) {
                        $payment->update(['status' => 'fail']);
                    }
                }
                $buffer->delete();
            }
        }
    }
}
