<?php

namespace App\Services\Platform;

use App\Http\Requests\Platform\OfferController\CreateRequest;
use App\Jobs\Platform\SearchAdsJob;
use App\Models\Ad;
use App\Models\File;
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
        $data['pay_format'] = json_encode($request->pay_format);

        //Проверяем на уникальность!
        $adCheck = Ad::where('name', $data['name'])
            ->where('type_id', $data['type_id'])
            ->where('inventory', $data['inventory'])
            ->whereJsonContains('pay_format', json_decode($data['pay_format']))
            ->where('region_id', $data['region_id'])
            ->where('budget', $data['budget'])
            ->where('start_date', $data['start_date'])
            ->where('end_date', $data['end_date'])
            ->where('user_id', $data['user_id'])
            ->where('is_offer', $is_offer)
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
            $data['is_offer'] = $is_offer;
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

    public function update(int $id, $request, $isAdmin = false)
    {
        $ad = Ad::findOrFail($id);
        if(!$isAdmin && $ad->user_id != Auth()->id()){
            return Response()->json(['message' => 'Forbidden', 'errors' => ['error' => 'Forbidden']], 403);
        }

        $data = $request->validated();

        unset($data['document']);
        unset($data['photo']);

        $data['pay_format'] = json_encode($request->pay_format);

        $adCheck = Ad::where('name', $data['name'])
            ->where('type_id', $data['type_id'])
            ->where('inventory', $data['inventory'])
            ->whereJsonContains('pay_format', json_decode($data['pay_format']))
            ->where('region_id', $data['region_id'])
            ->where('budget', $data['budget'])
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

        $ad->update($data);

        SearchAdsJob::dispatch($ad);

        return Response()->json([
            'message' => 'true',
            'ad' => $ad->load('photo', 'document')
        ], 200);
    }
}
