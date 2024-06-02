<?php

namespace App\Services\Platform;

use App\Http\Requests\Platform\OfferController\CreateRequest;
use App\Jobs\Platform\SearchAdsJob;
use App\Models\Ad;
use App\Models\File;
use Illuminate\Support\Facades\DB;

class OfferService
{

    //При размещении предложения нужно искать похожие запросы и отпралять их юзеру в уведомлении
    //(Ссылка будет тупо на фильтры) ++++
    //При размещении нужно проверять нет-ли у юзера такого же объявления (но если город разный то ок) и запрещать ему его добалвять ++++

    //Форму оплаты можно выбрать разную (Деньги и бартер), но если выбран "обмен рекламным трафиком" то выбран может быть только он!+++
    //Если выбран слив, то только деньги!!+++

    //Обмен реклам трафик это без бартера и без слива!!!+++

    # ОБЪЯВЛЕНИЕ РАЗМЕЩАЕТСЯ НА 1 МЕСЯЦ, ЗАТЕМ АРХИВ НА ПОЛ ГОДА, ПОСЛЕ УДАЛЕНИЕ!++++

    #TODO Фото объявления это ЛОГО компании (либо лого борт пресса)

    public function create(CreateRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = Auth()->id();

        unset($data['document']);
        unset($data['photo']);
        $data['pay_format'] = json_encode($request->pay_format);

        //Проверяем на уникальность!
        $adCheck = Ad::where('name', $data['name'])
            ->where('type_id', $data['type_id'])
            ->where('inventory', $data['inventory'])
            ->whereJsonContains('pay_format', $data['pay_format'])
            ->where('region_id', $data['region_id'])
            ->where('budget', $data['budget'])
            ->where('start_date', $data['start_date'])
            ->where('end_date', $data['end_date'])
            ->where('user_id', $data['user_id'])
            ->exists();

        $paySlugs = DB::table('pay_formats')
            ->whereIn('id', json_decode($data['pay_format']))
            ->pluck('slug')
            ->all();

        // Проверка форматов оплаты
        if(in_array('trade', $paySlugs) && count($paySlugs) > 1){
            return Response()->json(['message' => 'При формате оплаты "обмен рекламным трафиком" нельзя выбрать другие варианты', 'errors' => ['error' => 'При формате оплаты "обмен рекламным трафиком" нельзя выбрать другие варианты']], 422);
        }
        if(in_array('sliv', $paySlugs)){
            unset($paySlugs['cash']);
            if(count($paySlugs) > 0){
                return Response()->json(['message' => 'Вместе со сливом можно выбрать только денежные средства', 'errors' => ['error' => 'При формате оплаты "обмен рекламным трафиком" нельзя выбрать другие варианты']], 422);
            }
        }
        if(in_array('trade', $paySlugs) ){
            unset($paySlugs['cash']);
            if(count($paySlugs) > 0){
                return Response()->json(['message' => 'Вместе с "Обмен рекламным трафиком" можно выбрать только денежные средства', 'errors' => ['error' => 'При формате оплаты "обмен рекламным трафиком" нельзя выбрать другие варианты']], 422);
            }
        }

        if (!$adCheck) {
            $ad = Ad::create($data);
            if($request->hasFile('document')) {
                File::create([
                    'fileable_id' => $ad->id,
                    'fileable_type' => 'App\Models\Ad',
                    'category' => 'document',
                    'src' => env('APP_URL').$request->file('document')->store('documents', 'public')
                ]);
            }

            if($request->hasFile('photo')) {
                File::create([
                    'fileable_id' => $ad->id,
                    'fileable_type' => 'App\Models\Ad',
                    'category' => 'photo',
                    'src' => env('APP_URL').$request->file('photo')->store('photos', 'public')
                ]);
            }

            SearchAdsJob::dispatch($ad);
        }else{
            return Response()->json(['message' => 'Такое предложение уже существует', 'errors' => ['error' => 'Такое предложение уже существует']], 422);
        }

        return Response()->json([
            'message' => 'true',
            'ad' => $ad->load('photo', 'document')
        ], 201);
    }
}
