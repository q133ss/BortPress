<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdController\StoreRequest;
use App\Http\Requests\Platform\OfferController\CreateRequest;
use App\Jobs\Platform\SearchAdsJob;
use App\Models\Ad;
use App\Models\File;
use App\Models\User;
use App\Services\Platform\OfferService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdController extends Controller
{
    public function index()
    {
        return Ad::orderBy('created_at', 'desc')->get();
    }

    public function store(StoreRequest $request)
    {
        $data = $request->validated();

        unset($data['document']);
        unset($data['photo']);
        $data['pay_format'] = json_encode($request->pay_format);

        //Проверяем на уникальность!
        $adCheck = Ad::where('name', $data['name'])
            ->where('type_id', $data['type_id'])
//            ->whereJsonContains('inventory', $data['inventory'])
//            ->whereJsonContains('pay_format', $data['pay_format'])
            ->whereJsonContains('inventory', json_decode($data['inventory']))
            ->whereJsonContains('pay_format', json_decode($data['pay_format']))
            ->where('region_id', $data['region_id'])
            ->where('budget', $data['budget'])
            ->where('start_date', $data['start_date'])
            ->where('end_date', $data['end_date'])
            ->where('user_id', $data['user_id'])
            ->where('is_offer', $data['is_offer'])
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
            $data['is_offer'] = $data['is_offer'];
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
        }else{
            return Response()->json(['message' => 'Такое предложение уже существует', 'errors' => ['error' => 'Такое предложение уже существует']], 422);
        }

        return Response()->json([
            'message' => 'true',
            'ad' => $ad->load('photo', 'document'),
            'items' => $ad->item()
        ], 201);
    }

    public function update(int $id, CreateRequest $request)
    {
        return (new OfferService())->update($id, $request, true);
    }

    public function show($id)
    {
        $ad = Ad::findOrFail($id)->load('photo', 'document');

        return Response()->json([
            'ad' => $ad->item(),
            'items' => $ad->item()
        ]);
    }

    public function users()
    {
        return User::orderBy('created_at', 'DESC')->get();
    }

    public function delete($id)
    {
        Ad::findOrFail($id);
        return Response()->json([
            'message' => 'true'
        ]);
    }
}
