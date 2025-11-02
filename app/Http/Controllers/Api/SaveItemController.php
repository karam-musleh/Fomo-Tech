<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\BlogResource;
use Illuminate\Support\Facades\Auth;
use App\Http\Traits\ApiResponserTrait;
use App\Http\Resources\ArticleResource;


class SaveItemController extends Controller
{
    use ApiResponserTrait;
    public function saveArticle($articleId)
    {
        $user = Auth::guard('api')->user() ?? request()->user();
        if (!$user) return $this->errorResponse('Unauthenticated', 401);


        if ($user->savedArticles()->where('article_id', $articleId)->exists()) {
            return $this->errorResponse('Article already saved', 409);
        }
        $user->savedArticles()->syncWithoutDetaching([$articleId]);

        return $this->successResponse(null, 'Article saved successfully', 201);
    }
    public function unsaveArticle($articleId)
    {
        $user = Auth::guard('api')->user() ?? request()->user();
        if (!$user) return $this->errorResponse('Unauthenticated', 401);

        if (!$user->savedArticles()->where('article_id', $articleId)->exists()) {
            return $this->errorResponse('Article not found in saved items', 400);
        }

        $user->savedArticles()->detach($articleId);

        return $this->successResponse(null, 'Article removed from saved items successfully');
    }
    public function saveBlog($blogId)
    {
        $user = Auth::guard('api')->user() ?? request()->user();
        if (!$user) return $this->errorResponse('Unauthenticated', 401);

        if ($user->savedBlogs()->where('blog_id', $blogId)->exists()) {
            return $this->errorResponse('Blog already saved', 409);
        }

        $user->savedBlogs()->syncWithoutDetaching([$blogId]);
        return $this->successResponse(null, 'Blog saved successfully', 201);
    }
    public function unsaveBlog($blogId)
    {
        $user = Auth::guard('api')->user() ?? request()->user();
        if (!$user) return $this->errorResponse('Unauthenticated', 401);

        if (!$user->savedBlogs()->where('blog_id', $blogId)->exists()) {
            return $this->errorResponse('Blog not found in saved items', 400);
        }

        $user->savedBlogs()->detach($blogId);
        return $this->successResponse(null, 'Blog removed from saved items successfully');
    }


    public function getSaved(Request $request)
    {
        $user = request()->user('api') ?? Auth::guard('api')->user();
        if (!$user) return $this->errorResponse('Unauthenticated', 401);

        $type = request()->query('type');
        $perPage = $request->query('per_page', 10);

        if ($type == 'article') {
            $articles = $user->savedArticles()->paginate($perPage);
            return $this->successResponse(['Articles' => ArticleResource::collection($articles)], 200);
        }

        if ($type == 'blog') {
            $blogs = $user->savedBlogs()->paginate($perPage);
            return $this->successResponse(['Blogs' => BlogResource::collection($blogs)], 200);
        }
        $articles = $user->savedArticles()->paginate($perPage);
        $blogs = $user->savedBlogs()->paginate($perPage);

        return $this->successResponse([
            'articles' => ArticleResource::collection($articles),
            'blogs' => BlogResource::collection($blogs),
        ], 200);
    }
}
