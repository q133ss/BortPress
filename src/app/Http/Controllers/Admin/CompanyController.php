<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CompanyController\StoreRequest;
use App\Models\Company;
use App\Models\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Company::orderBy('created_at')->get();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRequest $request)
    {
        $data = $request->validated();

        unset($data['logo']);
        unset($data['documents']);

        $company = Company::create($data);

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

        return response()->json($company->load('logo', 'documents', 'user'), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return Company::findOrFail($id)->load('logo', 'documents', 'user');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreRequest $request, string $id)
    {
        $company = Company::findOrFail($id);

        $data = $request->validated();
        unset($data['logo']);
        unset($data['documents']);

        $update = $company->update($data);

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

        return response()->json($company->load('logo', 'documents', 'user'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $company = Company::findOrFail($id);
        $company->delete();
        return Response()->json([
            'message' => 'true'
        ]);
    }
}
