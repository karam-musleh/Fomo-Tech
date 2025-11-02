<?php

namespace App\Http\Controllers\Api;

use App\Models\Track;
use App\Models\Article;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponserTrait;
use App\Http\Resources\ArticleResource;
use App\Http\Requests\StoreArticleRequest;
use App\Http\Requests\UpdateArticleRequest;
use Illuminate\Foundation\Providers\ArtisanServiceProvider;

class ArticleController extends Controller
{
    use ApiResponserTrait;

    //index
    public function index()
    {
        $articles = Article::with('mentor.user', 'tracks')
            ->where('status', Article::STATUS_ACCEPTED)->get();
        return $this->successResponse(ArticleResource::collection($articles));
    }
    // show
    public function show($slug)
    {
        $user = auth()->guard('api')->user();

        $canAccessAdmin = $user?->role === 'admin';
        $canAccessMentor = $user?->mentor?->articles()->where('slug', $slug)->exists();
        $query = Article::with(['mentor.user', 'tracks'])
            ->where('slug', $slug);
        // dd($canAccessAdmin , $canAccessMentor);
        if (!$canAccessAdmin || !$canAccessMentor) {
            $query->where('status', Article::STATUS_ACCEPTED);
        }

        $article = $query->first();
        if (!$article) {
            return $this->errorResponse('Article not found', 404);
        }
        return $this->successResponse(
            new ArticleResource($article),
            'Article retrieved successfully.'
        );
    }


    // add article by mentor to  tracks
    public function store(StoreArticleRequest $request, $track_id)
    {
        $user = auth()->guard('api')->user();

        if (!$user) {
            return $this->errorResponse('User not authenticated.', 401);
        }

        if (!$user->mentor) {
            return $this->errorResponse('You must be a mentor to create an article.', 403);
        }

        $mentor = $user->mentor;

        $track = Track::find($track_id);
        if (!$track) {
            return $this->errorResponse('Track not found.', 404);
        }

        $hasTrack = $mentor->tracks()->where('tracks.id', $track->id)->exists();
        if (!$hasTrack) {
            return $this->errorResponse('You cannot add an article to a track that does not belong to you.', 403);
        }

        $article = Article::create([
            'title' => $request->title,
            'content' => $request->content,
            'mentor_id' => $mentor->id,
        ]);

        $article->tracks()->syncWithoutDetaching([$track->id]);

        $article->load(['mentor.user', 'tracks']);

        return $this->successResponse(new ArticleResource($article), 'Article created successfully.', 201);
    }

    public function myArticles(Request $request)
    {
        $user = auth()->guard('api')->user();

        if (!$user || (!$user->mentor && $user->role !== 'admin')) {
            return $this->errorResponse('Only mentors or admins can view this list.', 403);
        }

        $status = $request->input('status');
        $perPage = $request->input('per_page', 4);

        if ($user->role === 'admin') {
            $query = Article::with(['tracks:id,name', 'mentor.user:id,first_name,last_name,profile_photo'])
                ->orderBy('created_at', 'desc');
        } else {
            $query = $user->mentor->articles()
                ->with(['tracks:id,name', 'mentor.user:id,first_name,last_name,profile_photo'])
                ->orderBy('created_at', 'desc');
        }

        if ($status) {
            switch ($status) {
                case 'accepted':
                    $query->where('status', Article::STATUS_ACCEPTED);
                    break;
                case 'rejected':
                    $query->where('status', Article::STATUS_REJECTED);
                    break;
                case 'under_review':
                    $query->where('status', Article::STATUS_UNDER_REVIEW);
                    break;
                default:
                    return $this->errorResponse('Invalid status value.', 400);
            }
        }

        $articles = $query->paginate($perPage);

        return $this->successResponse(
            ArticleResource::collection($articles),
            'Articles retrieved successfully.'
        );
    }


    // update
    public function update(UpdateArticleRequest $request, $slug)
    {
        $user = auth()->guard('api')->user();

        if (!$user || ($user->role !== 'admin' && !$user->mentor)) {
            return $this->errorResponse('You must be a mentor or admin to update an article.', 403);
        }

        // جلب المقال بناءً على الدور
        $articleQuery = Article::query();
        if ($user->role === 'mentor') {
            $articleQuery->where('mentor_id', $user->mentor->id)
                ->where('status', 'accepted'); // إذا أردت السماح فقط بالمقالات المقبولة
        }
        $article = $articleQuery->find($slug);

        if (!$article) {
            return $this->errorResponse('Article not found or not accessible.', 404);
        }

        $article->update($request->validated());
        $article->refresh()->load(['mentor.user', 'tracks']);

        return $this->successResponse(new ArticleResource($article), 'Article updated successfully.');
    }



    // delete
    public function destroy($slug)
    {
        $user = auth()->guard('api')->user();
        if (!$user || !$user->mentor) {
            return $this->errorResponse('You must be a mentor to delete an article.', 403);
        }

        $article = Article::find($slug);
        if (!$article) {
            return $this->errorResponse('Article not found.', 404);
        }
        if ($user->role !== 'admin' && $article->mentor_id !== $user->mentor->id) {
            return $this->errorResponse('You can only delete your own articles.', 403);
        }
        $article->delete();

        return $this->successResponse(null, 'Article deleted successfully.', 200);
    }

    public function accepted($slug)
    {
        $article  = Article::where('slug', $slug)->first();
        if (!$article) {
            return $this->errorResponse('Article Not Found', 404);
        }
        $article->update([
            'status' => Article::STATUS_ACCEPTED,
            'rejection_reason' => null,
        ]);
        return $this->successResponse(null, 'Article Accepted Successfully', 200);
    }
    public function rejected(Request $request, $slug)
    {
        $article  = Article::where('slug', $slug)->first();
        if (!$article) {
            return $this->errorResponse('Article Not Found', 404);
        }
        $request->validate([
            'rejection_reason' => 'required|string',
        ]);
        $article->update([
            'status' => Article::STATUS_REJECTED,
            'rejection_reason' => $request->rejection_reason,
        ]);
        return $this->successResponse($article, 'Article Rejected Successfully', 201);
    }
    public function underReview($slug)
    {
        $article  = Article::where('slug', $slug)->first();
        if (!$article) {
            return $this->errorResponse('Article Not Found', 404);
        }
        $article->update([
            'status' => Article::STATUS_UNDER_REVIEW,
            'rejection_reason' => null,
        ]);
        return $this->successResponse(null, 'Article Under Review Successfully', 200);
    }


    public function getTodaysPublishedCount()
    {
        $count = Article::query()
            ->where('status', 'published')
            ->whereDate('created_at', Carbon::today())
            ->count();
        return $this->successResponse($count, "todays_published_articles");
    }
}
