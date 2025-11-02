<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\Article;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponserTrait;
use App\Http\Resources\ArticleResource;


class AdminArticleController extends Controller
{
     use ApiResponserTrait;
    /**
     * Display a listing of the resource.
     */
   public function index()
    {
        $articles = Article::with('mentor.user', 'tracks')->get();
        return $this->successResponse(ArticleResource::collection($articles));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
