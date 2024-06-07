<?php

namespace App\Services;

use App\Http\Requests\ProfileController\UpdateRequest;
use App\Models\Company;
use App\Models\File;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileService
{
    public function update(UpdateRequest $request)
    {
        $user = Auth()->user();

        $userData = [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
        ];

        try {
            DB::beginTransaction();

            if ($request->password != null) {
                $userData['password'] = Hash::make($request->password);
            }

            $user->update($userData);

            $company = $user->company;

            if ($company == null) {
                $company = Company::create([
                    'name' => $request->company_name,
                    'user_id' => $user->id,
                    'inn' => $request->inn,
                    'kpp' => $request->kpp,
                    'ogrn' => $request->ogrn,
                    'fact_address' => $request->fact_address,
                    'ur_address' => $request->ur_address,
                    'region_id' => $request->region_id,
                    'site_url' => $request->site_url,
                    'description' => $request->description
                ]);
            } else {
                $company->update([
                    'name' => $request->company_name,
                    'inn' => $request->inn,
                    'kpp' => $request->kpp,
                    'ogrn' => $request->ogrn,
                    'fact_address' => $request->fact_address,
                    'ur_address' => $request->ur_address,
                    'region_id' => $request->region_id,
                    'site_url' => $request->site_url,
                    'description' => $request->description
                ]);
            }

            if ($request->hasFile('documents')) {
                $oldFile = File::where('fileable_id', $company->id)
                    ->where('fileable_type', 'App\Models\Company')
                    ->where('category', 'documents');

                if ($oldFile->exists()) {
                    Storage::disk('public')->delete(str_replace(env('APP_URL').'/storage/', '', $oldFile->pluck('src')->first()));
                    $oldFile->delete();
                }

                File::create([
                    'fileable_id' => $company->id,
                    'fileable_type' => 'App\Models\Company',
                    'category' => 'documents',
                    'src' => env('APP_URL') . '/storage/' . $request->file('documents')->store('documents', 'public')
                ]);
            }

            if ($request->hasFile('logo')) {
                $oldLogo = File::where('fileable_id', $company->id)
                    ->where('fileable_type', 'App\Models\Company')
                    ->where('category', 'logo');

                if ($oldLogo->exists()) {
                    Storage::disk('public')->delete(str_replace(env('APP_URL').'/storage/', '', $oldLogo->pluck('src')->first()));
                    $oldLogo->delete();
                }

                File::create([
                    'fileable_id' => $company->id,
                    'fileable_type' => 'App\Models\Company',
                    'category' => 'logo',
                    'src' => env('APP_URL') . '/storage/' . $request->file('logo')->store('logo', 'public')
                ]);
            }

            DB::commit();
        }catch (\Exception $e){
            return Response()->json(['message' => 'Произошла ошибка, попробуйте еще раз', 'errors' => ['error' => 'Произошла ошибка, попробуйте еще раз']], 422);
        }

        return Response()->json([
            'status' => 'true',
            'user' => $user,
            'company' => $company->load('documents', 'logo')
        ]);
    }
}
