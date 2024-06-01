<?php

namespace App\Jobs\Platform;

use App\Models\Ad;
use App\Services\NotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SearchAdsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private Ad $ad;

    /**
     * Create a new job instance.
     */
    public function __construct(Ad $ad)
    {
        $this->ad = $ad;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Ищем похожее и создаем уведомление
        $results = Ad::where('type_id', $this->ad->type_id)
        ->where('inventory', $this->ad->inventory)
        ->where('pay_format', $this->ad->pay_format)
        ->where('start_date', '>=' , $this->ad->start_date)
        ->where('end_date', '<=', $this->ad->end_date)
        ->where('region_id', $this->ad->region_id)
        ->count();

        if($results != 0){
            // Создаем уведомление
            $title = 'Новое совпадение!';
            $text = "Найдено $results совпадений по вашему запросу";
            $link = env('APP_URL')."/offers?type_id=".$this->ad->type_id.
            "&inventory=".$this->ad->inventory.
            "&pay_format=".$this->ad->pay_format.
            "&start_date=".$this->ad->start_date.
            "&end_date=".$this->ad->end_date.
            "&region_id=".$this->ad->region_id;

            (new NotificationService())->create($title, $text, [$this->ad->user_id], $link);
        }
    }
}
