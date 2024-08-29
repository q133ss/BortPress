<?php

namespace App\Services\Platform;

use App\Http\Requests\Platform\OfferController\CreateRequest;
use App\Jobs\Platform\SearchAdsJob;
use App\Models\Ad;
use App\Models\File;
use App\Models\PayFormat;
use App\Models\Region;
use App\Models\Type;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class OfferService
{
    public function create($request, bool $is_offer = true)
    {
        $data = $request->validated();
        $data['user_id'] = Auth()->id();

        unset($data['document']);
        unset($data['photo']);

        unset($data['cost_by_price']);
        unset($data['discount_cost']);
        unset($data['possibility_of_extension']);

//        $data['pay_format'] = json_encode($request->pay_format);
        #todo теперь это не массив
        //$data['pay_format'] = json_decode($request->pay_format);
        $data['is_selling'] = 0;

        unset($data['region_id']);
        //$data['region_id'] = Region::pluck('id')->first();
        $data['regions'] = (array) $request->region_id;

        //Проверяем на уникальность!
        $adCheck = Ad::where('name', $data['name'])
            ->where('type_id', $data['type_id'])
            #TODO нужен тест
            //->where('inventory', $data['inventory'])
            ->whereJsonContains('inventory', $data['inventory'] ?? null)
            ->whereJsonContains('pay_format', $data['pay_format'])
            ->whereJsonContains('regions', $data['regions'])
            ->where('budget', $data['budget'] ?? null)
            ->where('start_date', $data['start_date'])
            ->where('end_date', $data['end_date'])
            ->where('user_id', $data['user_id'])
            ->where('is_offer', $is_offer)
            ->exists();

        $paySlugs = DB::table('pay_formats')
            ->where('id', $data['pay_format'])
            ->pluck('slug')
            ->all();

        // Проверка форматов оплаты
        if(in_array('trade', $paySlugs) && count($paySlugs) > 1){
            return Response()->json(['message' => 'При формате оплаты "обмен рекламным трафиком" нельзя выбрать другие варианты', 'errors' => ['error' => 'При формате оплаты "обмен рекламным трафиком" нельзя выбрать другие варианты']], 422);
        }
//        if(in_array('sliv', $paySlugs)){
//            unset($paySlugs['cash']);
//            if(count($paySlugs) > 0){
//                return Response()->json(['message' => 'Вместе со сливом можно выбрать только денежные средства', 'errors' => ['error' => 'При формате оплаты "обмен рекламным трафиком" нельзя выбрать другие варианты']], 422);
//            }
//        }
        if(in_array('trade', $paySlugs) ){
            unset($paySlugs['cash']);
            if(count($paySlugs) > 0){
                return Response()->json(['message' => 'Вместе с "Обмен рекламным трафиком" можно выбрать только денежные средства', 'errors' => ['error' => 'При формате оплаты "обмен рекламным трафиком" нельзя выбрать другие варианты']], 422);
            }
        }

        if (!$adCheck) {
            $data['is_offer'] = $is_offer;
            $ad = Ad::create($data);
            if($request->hasFile('document')) {
                File::create([
                    'fileable_id' => $ad->id,
                    'fileable_type' => 'App\Models\Ad',
                    'category' => 'document',
                    'src' => env('APP_URL').'/storage/'.$request->file('document')->store('documents', 'public')
                ]);
            }

            if($request->hasFile('photo')) {
                File::create([
                    'fileable_id' => $ad->id,
                    'fileable_type' => 'App\Models\Ad',
                    'category' => 'photo',
                    'src' => env('APP_URL').'/storage/'.$request->file('photo')->store('photos', 'public')
                ]);
            }

            SearchAdsJob::dispatch($ad);
        }else{
            return Response()->json(['message' => 'Такое предложение уже существует', 'errors' => ['error' => 'Такое предложение уже существует']], 422);
        }

        return Response()->json([
            'message' => 'true',
            'ad' => $ad->load('photo', 'document')->setRelation('regions', $ad->getRegions())
        ], 201);
    }

    public function update(int $id, $request, $isAdmin = false)
    {
        $ad = Ad::findOrFail($id);
        if(!$isAdmin && $ad->user_id != Auth()->id()){
            return Response()->json(['message' => 'Forbidden', 'errors' => ['error' => 'Forbidden']], 403);
        }

        $data = $request->validated();

        unset($data['document']);
        unset($data['photo']);

        $sliv_id = PayFormat::where('slug', 'sliv')->pluck('id')->first();

        //$payFormats = $ad->getAttribute('pay_format')->pluck('id')->all();
        $payFormats = $data['pay_format'];

        if(!in_array($sliv_id, $payFormats))
        {
            unset($data['cost_by_price']);
            unset($data['discount_cost']);
            unset($data['possibility_of_extension']);
        }else{
            $request->validate(
                [
                    'cost_by_price' => 'required',
                    'discount_cost' => 'required',
                    'possibility_of_extension' => 'required'
                ],
                [
                    'cost_by_price.required' => 'Поле "Цена по прайсу" обязательно для заполнения.',
                    'discount_cost.required' => 'Поле "Цена со скидкой" обязательно для заполнения.',
                    'possibility_of_extension.required' => 'Поле "Возможность продления" обязательно для заполнения.',
                ]
            );
        }

        $data['pay_format'] = json_encode($request->pay_format);

        $adCheck = Ad::where('name', $data['name'])
            ->where('type_id', $data['type_id'])
            ->where('inventory', $data['inventory'] ?? null)
            ->whereJsonContains('pay_format', json_decode($data['pay_format']))
            ->where('region_id', $data['region_id'])
            ->where('budget', $data['budget'] ?? null)
            ->where('start_date', $data['start_date'])
            ->where('end_date', $data['end_date'])
            ->where('user_id', Auth()->id())
            ->exists();

        if($adCheck){
            return Response()->json(['message' => 'Такое предложение уже существует', 'errors' => ['error' => 'Такое предложение уже существует']], 422);
        }

        $paySlugs = DB::table('pay_formats')
            ->whereIn('id', json_decode($data['pay_format']))
            ->pluck('slug')
            ->all();

        // Проверка форматов оплаты
        if(in_array('trade', $paySlugs) && count($paySlugs) > 1){
            return Response()->json(['message' => 'При формате оплаты "обмен рекламным трафиком" нельзя выбрать другие варианты', 'errors' => ['error' => 'При формате оплаты "обмен рекламным трафиком" нельзя выбрать другие варианты']], 422);
        }
        if(in_array('sliv', $paySlugs)){
            $diff = array_diff($paySlugs, ['sliv', 'cash']);
            if(count($diff) > 0){
                return Response()->json(['message' => 'Вместе со сливом можно выбрать только денежные средства', 'errors' => ['error' => 'При формате оплаты "обмен рекламным трафиком" нельзя выбрать другие варианты']], 422);
            }
        }
        if(in_array('trade', $paySlugs) ){
            $diff = array_diff($paySlugs, ['trade', 'cash']);
            if(count($diff) > 0){
                return Response()->json(['message' => 'Вместе с "Обмен рекламным трафиком" можно выбрать только денежные средства', 'errors' => ['error' => 'При формате оплаты "обмен рекламным трафиком" нельзя выбрать другие варианты']], 422);
            }
        }

        if($request->hasFile('document')) {
            $oldFile = File::where([
                'fileable_id' => $ad->id,
                'fileable_type' => 'App\Models\Ad',
                'category' => 'document'
            ]);

            Storage::disk('public')->delete(str_replace(env('APP_URL').'/storage/', '', $oldFile->pluck('src')->first()));

            $oldFile->delete();

            File::create([
                'fileable_id' => $ad->id,
                'fileable_type' => 'App\Models\Ad',
                'category' => 'document',
                'src' => env('APP_URL').'/storage/'.$request->file('document')->store('documents', 'public')
            ]);
        }

        if($request->hasFile('photo')) {
            $oldFile = File::where([
                'fileable_id' => $ad->id,
                'fileable_type' => 'App\Models\Ad',
                'category' => 'photo'
            ]);
            Storage::disk('public')->delete(str_replace(env('APP_URL').'/storage/', '', $oldFile->pluck('src')->first()));
            $oldFile->delete();

            File::create([
                'fileable_id' => $ad->id,
                'fileable_type' => 'App\Models\Ad',
                'category' => 'photo',
                'src' => env('APP_URL').'/storage/'.$request->file('photo')->store('photos', 'public')
            ]);
        }
        unset($data['region_id']);
        $ad->update($data);

        SearchAdsJob::dispatch($ad);

        return Response()->json([
            'message' => 'true',
            'ad' => $ad->load('photo', 'document') //item
        ], 200);
    }

    public function delete(int $id)
    {
        $offer = Ad::findOrFail($id);
        if($offer->user_id != Auth('sanctum')->id()){
            abort(403);
        }
        $offer->update(['is_archive' => 1, 'archive_date' => now()]);
        return Response()->json([
            'message' => 'true'
        ]);
    }
}
