<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\Article;
use App\Models\Resource;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponserTrait;
use App\Http\Resources\ArticleResource;
use App\Http\Resources\ResourceResource;

class DashboardController extends Controller
{
    use ApiResponserTrait;

    public function mentorContents(Request $request)
    {
        $per_page = $request->input('per_page', 5);
        $status   = $request->input('status', 'all');


        $articlesQuery = Article::with(['mentor.user:id,first_name,last_name,profile_photo', 'tracks:id,name']);
        $resourcesQuery = Resource::with(['sections:id,title', 'tracks:id,name', 'mentor.user:id,first_name,last_name,profile_photo']);


        if ($status !== 'all') {
            $articlesQuery->where('status', $status);
            $resourcesQuery->where('status', $status);
        }

        // ✅ ترتيب وناتج
        $articles = $articlesQuery->latest()->paginate($per_page);
        $resources = $resourcesQuery->latest()->paginate($per_page);

        // ✅ الرد
        return $this->successResponse([
            'resources' => ResourceResource::collection($resources),
            'articles'  => ArticleResource::collection($articles),
        ], "Contents filtered by status: {$status}", 200);
    }


}
