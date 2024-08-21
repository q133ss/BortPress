<?php

namespace App\Console\Commands;

use App\Models\Ad;
use Illuminate\Console\Command;

class CheckArchive extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:archive';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Проверяет архив и удаляет старые записи';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Начинаю проверку архива');
        $ads = Ad::where('is_archive', true)
            ->where('archive_date', '<=', now()->subMonths(6));

        $this->info('Получено '.$ads->count(). ' записей');
        $ads->delete();
        $this->info('Записи удалены');
    }
}
