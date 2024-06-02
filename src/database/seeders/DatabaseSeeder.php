<?php

namespace Database\Seeders;

use App\Models\Region;
use App\Models\Role;
use App\Models\Type;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use SplFileObject;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $roles = [
            'Рекламодатель' => 'advertiser',
            'Рекламная площадка' => 'adv_platform',
            'Админ' => 'admin'
        ];

        foreach ($roles as $name => $slug){
            Role::create(['name' => $name, 'slug' => $slug]);
        }

        User::create([
            'name' => 'user',
            'email' => 'user@email.net',
            'password' => '$2y$12$mNAx8Lbawen/b7Od0wMPveV4/0G.decRsZEvZIHKGra5EL41Hj9oK',
            'role_id' => '1',
            'phone' => '+7(999)-999-99-00'
        ]);

        $formats = [
            'cash' => 'Денежные средства',
            'barter' => 'Бартер',
            'trade' => 'Обмен рекламным трафиком',
            'sliv' => 'Слив'
        ];

        foreach ($formats as $slug => $name) {
            DB::table('pay_formats')
                ->insert(['slug' => $slug, 'name' => $name]);
        }

        $types = [
            'Наружная реклама' => [
                'Рекламный щит',
                'Призматрон',
                'Настенная вывеска',
                'Цифровая наружная реклама',
                'Автомобильная реклама',
                'Другое'
            ],
            'Радиореклама' => [
                'Рекламные анонсы',
                'Рекламные блоки в эфире',
                'Интегрированная реклама',
                'Контекстуальная радиореклама'
            ],
            'Телевизионная реклама' => [
                'Рекламные анонсы',
                'Рекламные блоки в эфире',
                'Рекламные ролики',
                'Продажа брендированных контентов'
            ],
            'Интернет реклама' => [
                'Таргетированная реклама',
                'Контекстная реклама',
                'SEO',
            ]
        ];

        foreach ($types as $name => $children){
            $parent = Type::create(['name' => $name, 'parent_id' => null]);
            foreach ($children as $child){
                Type::create(['name' => $child, 'parent_id' => $parent->id]);
            }
        }

        // Регионы
        $filePath = storage_path('app/region.csv');

        $regions = [];
        $file = new SplFileObject($filePath, 'r');

        while (!$file->eof()) {
            $rowData = $file->fgetcsv();
            // Извлекаем название региона из нужного столбца (например, столбец 2)
            $regionName = $rowData[2] ?? '';
            // Добавляем название региона в массив
            $regions[] = $regionName;
        }

        // Закрываем файл
        $file = null;

        foreach ($regions as $region){
            Region::create(['name' => $region]);
        }

        Region::find(1)->delete();
        Region::find(15)->update(['name' => str_replace('/Якутия/','/ Якутия', Region::find(15)->name)]);
        Region::find(88)->delete();
    }
}
