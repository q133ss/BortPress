<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CompanyController\StoreRequest;
use App\Models\Company;
use Illuminate\Http\Request;

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
        $company = Company::create($request->validated());
        return response()->json($company, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return Company::findOrFail($id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreRequest $request, string $id)
    {
        $company = Company::findOrFail($id);
        $company->update($request->validated());
        return response()->json($company);
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
