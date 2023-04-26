<?php

namespace App\Http\Controllers;

use App\Http\Requests\category\CreateCategoryRequest;
use App\Http\Resources\UniversalResource;
use App\Models\ApplicationCategory;
use Illuminate\Http\Request;

class ApplicationCategoryController extends Controller
{
    public function __construct(
        private ApplicationCategory $applicationCategory
    )
    {}

    public function index()
    {
        $search = request('search');
        $data = $this->applicationCategory
            ->where('title', 'LIKE', "%$search%")
            ->with(['type_count'])
            ->orderBy('id', 'asc')
            ->paginate(env('PG', 10));
        return UniversalResource::collection($data);
    }

    public function store(CreateCategoryRequest $request)
    {
        $request = $request->validated();

        $category = $this->applicationCategory->create([
            'title' => $request['title']
        ]);
        $category['type_count'] = ['category_id' => $category->id, 'count' => 0];

        return response()->json(['message' => env('MESSAGE_SUCCESS'), 'data' => $category], 201);
    }

    public function show($id)
    {
        return $this->applicationCategory
            ->with(['types'])
            ->where('id', $id)
            ->first();
    }

    public function update(CreateCategoryRequest $request, $id)
    {
        $request = $request->validated();

        $category = $this->applicationCategory->where('id', $id)->first();
        $category->update([
            'title' => $request['title']
        ]);
        return response()->json(['message' => env('MESSAGE_SUCCESS'), 'data' => $category], 200);
    }

    public function destroy($id)
    {
        $this->applicationCategory->where('id', $id)->delete();
        return response()->json(['data' => ['message' => env('MESSAGE_SUCCESS')]], 200);
    }

    public function getAll(){
        $data = $this->applicationCategory
            ->get();
        return $data;
    }
}
