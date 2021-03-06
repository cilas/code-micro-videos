<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryRequest;
use App\Models\Category;

abstract class BasicCrudController extends Controller
{

    protected abstract function model();

    public function index()
    {
        return $this->model()::all();
    }

    public function store(CategoryRequest $request)
    {
        $category = Category::create($request->validated());
        $category->refresh();
        return $category;
    }

    public function show(Category $category)
    {
        return $category;
    }

    public function update(CategoryRequest $request, Category $category)
    {
        $category->update($request->validated());
        return $category;
    }

    public function destroy(Category $category)
    {
        $category->delete();
        return  response()->noContent();
    }
}
