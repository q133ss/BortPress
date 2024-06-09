<?php

namespace App\Console\Commands;

use App\Services\Subscribe\SubscribeService;
use Illuminate\Console\Command;

class SubscribeCheck extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscribe:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Проверка оплаты подписок';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $service = new SubscribeService();
        $notPaid = $service->checkAll();
        $attemptPay = $service->attemptPay($notPaid);
        $service->checkPay();
        $this->info('Проверка запущена');
    }
}
