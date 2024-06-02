<?php

namespace App\Console\Commands;

use App\Models\Ad;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CheckAdsExpiration extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:ads';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Проверяет объявления на просрочку';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Начинаю проверку объявлений');
        $ads = Ad::where('created_at', '<=', Carbon::now()->subMonth());
        if($ads->exists()){
            foreach ($ads->get() as $ad){
                $ad->update(['is_archive' => true]);
                Log::build([
                    'driver' => 'single',
                    'path' => storage_path('logs/ads.log'),
                ])->info('Объявление ID: {id} заархивированною', ['id' => $ad->id]);
                $this->info("Объявление ID: $ad->id заархивированною");
            }
        }else{
            $this->info('Объявлений не найдено');
            Log::build([
                'driver' => 'single',
                'path' => storage_path('logs/ads.log'),
            ])->info('Объявлений не найдено');
        }

        $this->info('Все объявления проверены');

    }
}
