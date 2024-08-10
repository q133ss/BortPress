<?php

namespace Database\Seeders;

use App\Models\Ad;
use App\Models\Comment;
use App\Models\Company;
use App\Models\Item;
use App\Models\ItemCategory;
use App\Models\Notification;
use App\Models\Payment;
use App\Models\Region;
use App\Models\Role;
use App\Models\Tariff;
use App\Models\Type;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
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

        $usr = User::create([
            'name' => 'user',
            'email' => 'user@email.net',
            'password' => Hash::make('password'),
            'role_id' => '1',
            'phone' => '+7(999)-999-99-00',
            'subscribe_end' => now()->addDays(30)
        ]);

        Payment::create([
            'user_id' => $usr->id,
            'sum' => 999,
            'status' => 'done',
            'description' => 'Оплата подписки',
            'pay_id' => '312-654-32-545'
        ]);

        Company::create([
            'user_id' => $usr->id,
            'name' => 'ООО Рога и Копыта',
            'inn' => 777,
            'kpp' => 666,
            'ogrn' => 555,
            'fact_address' => "Воронеж, Ленина, 1",
            'ur_address' => "Воронеж, Ленина, 2",
            'region_id' => 37,
            'site_url' => "https://google.com",
            'description' => "Описание компании",
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

        $platform = User::create([
            'name' => 'Алексей',
            'email' => 'platform@email.net',
            'password' => Hash::make('password'),
            'role_id' => 2,
            'phone' => '+7(111)222-22-22',
            'subscribe_end' => now()->addDays(30)
        ]);

        Payment::create([
            'user_id' => $platform->id,
            'sum' => 999,
            'status' => 'done',
            'description' => 'Оплата подписки',
            'pay_id' => '312-654-32-545'
        ]);

        Company::create([
            'user_id'      => $platform->id,
            'name'         => 'ООО Какие Люди',
            'inn'          => '111',
            'kpp'          => '111',
            'ogrn'         => '111',
            'fact_address' => 'Воронеж, проспект Революции, 1',
            'ur_address'   => 'Москва, Красная Площадь 1',
            'region_id'    => 37,
            'site_url'     => 'https://google.com',
            'description'  => 'Описание'
        ]);

        $adv = User::create([
            'name' => 'Иван',
            'email' => 'adv@email.net',
            'password' => Hash::make('password'),
            'role_id' => 1,
            'phone' => '+7(111)222-33-33',
            'subscribe_end' => now()->addDays(30)
        ]);

        Payment::create([
            'user_id' => $adv->id,
            'sum' => 999,
            'status' => 'done',
            'description' => 'Оплата подписки',
            'pay_id' => '312-654-32-545'
        ]);

        Company::create([
            'user_id'      => $adv->id,
            'name'         => 'ООО Рога и Копыта',
            'inn'          => '222',
            'kpp'          => '222',
            'ogrn'         => '222',
            'fact_address' => 'Воронеж, проспект Революции, 2',
            'ur_address'   => 'Москва, Красная Площадь 2',
            'region_id'    => 37,
            'site_url'     => 'https://yandex.ru',
            'description'  => 'Описание123'
        ]);

        $tariffs = [
            'Обычный' => 999,
            'Не обычный' => 1999
        ];

        foreach ($tariffs as $name => $price){
            Tariff::create(['name' => $name, 'price' => $price]);
        }

        $item_categories = [
            'Оборудование и инструменты',
            'Автотовары',
            'Металлы',
            'Электроника и электротехника',
            'Одежда, обувь, игрушки',
            'Хозтовары, упаковка, канцелярия',
            'Строительные материалы',
            'Потреб товары и спецмагазины',
            'Текстиль для дома',
            'Техника для дома и офиса',
            'Мебель и фурнитура',
            'Спорт. Туризм. Отдых',
            'Здоровье и красота',
            'Строительство',
            'Полиграфия и реклама',
            'Продукты питания, напитки',
            'Химия и топливо',
            'Охрана и безопасность',
            'ИТ. Интернет. Связь',
            'Товары и услуги для животных',
            'Коммунальные услуги',
            'Транспортные услуги',
            'Юр., финансовые и бизнес услуги',
        ];

        foreach ($item_categories as $category){
            ItemCategory::create(['name' => $category]);
        }

        $metal_items = [
            'Листовой прокат',
            'Литейное производство',
            'Металлоизделия',
            'Металлообработка',
            'Металлопрокат',
            'Металлы и сплавы',
            'Нержавеющий металлопрокат',
            'Сварочные материалы и металлы',
            'Сортовый прокат',
            'Трубный прокат',
            'Трубопроводная и запорная арматура',
            'Ферросплавы',
            'Цветной металлопрокат'
        ];

        foreach ($metal_items as $item){
            Item::create([
                'category_id' => 3,
                'name' => $item
            ]);
        }

        $ads = [
            [
                'name' => 'Предложение 1',
                'type_id' => 2,
                'inventory' => json_encode([6,8]),
                'pay_format' => json_encode([1,2]),
                'region_id' => 37,
                'budget' => '300000',
                'start_date' => now()->format('Y-m-d'),
                'end_date' => now()->addDays(30)->format('Y-m-d'),
                'is_unique' => 1,
                'user_id' => 2,
                'additional_info' => 'Дополнительная информация',
                'link' => 'https://google.com',
                'is_offer' => 1,
                'is_selling' => 1,
                'is_archive' => 0
            ],

            [
                'name' => 'Предложение 2',
                'type_id' => 2,
                'inventory' => json_encode([1,2,4]),
                'pay_format' => json_encode([1,2]),
                'region_id' => 37,
                'budget' => '200000',
                'start_date' => now()->format('Y-m-d'),
                'end_date' => now()->addDays(30)->format('Y-m-d'),
                'is_unique' => 1,
                'user_id' => 2,
                'additional_info' => 'Дополнительная информация',
                'link' => 'https://google.com',
                'is_offer' => 1,
                'is_selling' => 0,
                'is_archive' => 0
            ],

            [
                'name' => 'Предложение 2',
                'type_id' => 2,
                'inventory' => json_encode([1]),
                'pay_format' => json_encode([1,2,4]),
                'region_id' => 37,
                'budget' => '300000',
                'start_date' => now()->format('Y-m-d'),
                'end_date' => now()->addDays(30)->format('Y-m-d'),
                'is_unique' => 1,
                'user_id' => 2,
                'additional_info' => 'Дополнительная информация',
                'link' => 'https://google.com',
                'is_offer' => 1,
                'is_selling' => 0,
                'is_archive' => 0
            ],

            [
                'name' => 'Предложение 3',
                'type_id' => 2,
                'inventory' => json_encode([5,7]),
                'pay_format' => json_encode([1,2]),
                'region_id' => 37,
                'budget' => '200000',
                'start_date' => now()->format('Y-m-d'),
                'end_date' => now()->addDays(30)->format('Y-m-d'),
                'is_unique' => 0,
                'user_id' => 2,
                'additional_info' => 'Дополнительная информация',
                'link' => 'https://google.com',
                'is_offer' => 0,
                'is_selling' => 1,
                'is_archive' => 0
            ],

            [
                'name' => 'Предложение 4',
                'type_id' => 2,
                'inventory' => json_encode([2,4]),
                'pay_format' => json_encode([1,2]),
                'region_id' => 37,
                'budget' => '600000',
                'start_date' => now()->format('Y-m-d'),
                'end_date' => now()->addDays(30)->format('Y-m-d'),
                'is_unique' => 0,
                'user_id' => 2,
                'additional_info' => 'Дополнительная информация',
                'link' => 'https://google.com',
                'is_offer' => 1,
                'is_selling' => 1,
                'is_archive' => 1
            ],
        ];

        foreach ($ads as $ad){
            unset($ad['pay_format']);
            $ad['pay_format'] = json_encode([1,2]);
            Ad::create($ad);
        }

        $admin = User::create([
            'name' => 'Админ',
            'email' => 'admin@email.net',
            'password' => Hash::make('password'),
            'role_id' => 3,
            'phone' => '+7(777)777-55-44',
            'subscribe_end' => now()->addYears(15)
        ]);

        Comment::create([
            'user_id' => $admin->id,
            'commentable_type' => 'App\Models\User',
            'commentable_id' => $usr->id,
            'comment' => 'Хороший продавец!'
        ]);

        Notification::create([
            'title' => 'Уведомление!',
            'text' => 'Текст уведомления',
            'link' => 'https://yandex.ru',
            'user_id' => $admin->id
        ]);

        Notification::create([
            'title' => 'Уведомление!',
            'text' => 'Текст уведомления',
            'link' => 'https://yandex.ru',
            'user_id' => $usr->id
        ]);

        Comment::create([
            'user_id' => $admin->id,
            'commentable_type' => 'App\Models\User',
            'commentable_id' => $adv->id,
            'comment' => 'Хороший продавец!!'
        ]);

        Comment::create([
            'user_id' => $admin->id,
            'commentable_type' => 'App\Models\User',
            'commentable_id' => $adv->id,
            'comment' => 'Не хороший продавец'
        ]);

        Notification::create([
            'title' => 'Уведомление!',
            'text' => 'Текст уведомления',
            'link' => 'https://yandex.ru',
            'user_id' => $adv->id
        ]);

        Payment::create([
            'user_id' => $admin->id,
            'sum' => 999,
            'status' => 'done',
            'description' => 'Оплата подписки',
            'pay_id' => '312-654-32-545'
        ]);

        Company::create([
            'user_id'      => $adv->id,
            'name'         => 'ООО Рога и Копыта',
            'inn'          => '222',
            'kpp'          => '222',
            'ogrn'         => '222',
            'fact_address' => 'Воронеж, проспект Революции, 2',
            'ur_address'   => 'Москва, Красная Площадь 2',
            'region_id'    => 37,
            'site_url'     => 'https://yandex.ru',
            'description'  => 'Описание'
        ]);
    }
}
