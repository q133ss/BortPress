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

class CheckPayJob implements ShouldQueue
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
        $service = new SubscribeService();
        foreach ($buffers as $buffer){
            $status = $service->checkStatus($buffer->pay_id);
            if($status){
                $p = Payment::find($buffer->pay_id);
                if($p != null) {
                    $p->update(['status' => 'done']);
                }
                $buffer->delete();
            }
        }
    }
}
