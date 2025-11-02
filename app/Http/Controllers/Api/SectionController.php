<?php

namespace App\Http\Controllers\Api;

use App\Models\Blog;
use App\Models\Section;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\BlogResource;
use App\Http\Traits\ApiResponserTrait;
use App\Http\Resources\SectionResource;
use App\Http\Resources\TrackResource;
use App\Models\Track;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class SectionController extends Controller
{
    use ApiResponserTrait;

    public function index()
    {
        $perPage = request()->input('per_page', 6);
        // $sections = Section::with(['resources:id','tracks:id,section_id'])->select('id', 'title', 'description')->paginate($perPage);
        $sections = Section::with(['resources:id', 'tracks'])->select('id', 'title', 'description')
            ->where('status', 'published')
            ->paginate($perPage);
        return $this->successResponse(SectionResource::collection($sections), 'Sections retrieved successfully.');
    }
public function show($slug)
{
    $user = Auth::guard('api')->user();
    if (!$user) {
        return $this->errorResponse('Unauthenticated', 401);
    }

    $perPage = request()->input('per_page', 3);

    $query = Section::with(['resources', 'tracks'])
        ->where('slug', $slug);

    if ($user->role !== 'admin') {
        $query->where('status', Section::STATUS_PUBLISHED);
    }

    $section = $query->first();
    if (!$section) {
        return $this->errorResponse('Section not found', 404);
    }

    $title = strtolower($section->title);

    if ($title === 'blog') {
        $blogs = Blog::select('id', 'title')->paginate($perPage);
        return $this->successResponse(BlogResource::collection($blogs), 'Blogs retrieved successfully.');
    }

    if ($title === 'tracks') {
        $tracks = Track::select('id', 'name')->paginate($perPage);
        return $this->successResponse(TrackResource::collection($tracks), 'Tracks retrieved successfully.');
    }

    return $this->successResponse(new SectionResource($section), 'Section retrieved successfully.');
}


}
