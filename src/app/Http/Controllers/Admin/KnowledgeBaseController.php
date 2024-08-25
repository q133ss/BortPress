<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\KnowledgeBaseController\KnowledgeBaseRequest;
use App\Models\File;
use App\Models\KnowledgeBase;
use Illuminate\Http\Request;

class KnowledgeBaseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return KnowledgeBase::with('file')->get();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(KnowledgeBaseRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = Auth('sanctum')->id();

        unset($data['file']);
        $knowledge = KnowledgeBase::create($data);

        if($request->hasFile('file')) {
            File::create([
                'fileable_type' => 'App\Models\KnowledgeBase',
                'fileable_id' => $knowledge->id,
                'category' => 'file',
                'src' => config('APP_URL').'/storage/'.$request->file('file')->store('messages', 'public')
            ]);
        }

        return $knowledge->load('file');

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return KnowledgeBase::where('user_id', Auth('sanctum')->id())->findOrFail($id)->load('file');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(KnowledgeBaseRequest $request, string $id)
    {
        $knowledgeBase = KnowledgeBase::where('user_id', Auth('sanctum')->id())->findOrFail($id);

        $knowledgeBase->update($request->validated());
        return response()->json([
            'message' => 'Статья успешно обновлена.',
            'data' => $knowledgeBase->load('file')
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $knowledgeBase = KnowledgeBase::where('user_id', Auth('sanctum')->id())->findOrFail($id);
        $knowledgeBase->delete();
        return Response()->json([
            'message' => 'true'
        ]);
    }
}
