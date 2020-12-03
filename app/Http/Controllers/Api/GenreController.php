<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\GenreRequest;
use App\Models\Genre;

class GenreController extends Controller
{

    public function index()
    {
        return Genre::all();
    }

    public function store(GenreRequest $request)
    {
        $genre = Genre::create($request->validated());
        $genre->refresh();
        return $genre;
    }

    public function show(Genre $genre)
    {
        return $genre;
    }

    public function update(GenreRequest $request, Genre $genre)
    {
        $genre->update($request->validated());
        return $genre;
    }

    public function destroy(Genre $genre)
    {
        $genre->delete();
        return response()->noContent();
    }
}
