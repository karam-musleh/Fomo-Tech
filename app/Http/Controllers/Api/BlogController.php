<?php

namespace App\Http\Controllers\Api;

use App\Models\Blog;
use App\Models\Section;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\BlogResource;
use App\Http\Traits\ApiResponserTrait;
use App\Http\Requests\StoreBlogRequest;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\UpdateBlogRequest;

class BlogController extends Controller
{
    use ApiResponserTrait;
    //index
    public function index()
    {
        $per_page = request()->input('per_page', 3);
        $blogs = Blog::with(['mentor.user:id', 'section:id'])
            ->where('status', Blog::STATUS_PUBLISHED)
            ->select('id', 'title', 'description', 'mentor_id', 'section_id')
            ->latest()
            ->paginate($per_page);
        return $this->successResponse(BlogResource::collection($blogs));
    }
    public function show($slug)
    {
        $blog = Blog::with(['mentor.user', 'section'])->where('slug', $slug)
            ->where('status', Blog::STATUS_PUBLISHED)
            ->first();
        if (!$blog) {
            return $this->errorResponse('Blog not found', 404);
        }

        return $this->successResponse(new BlogResource($blog), 'Blog retrieved successfully.');
    }

    public function store(StoreBlogRequest $request)
    {
        $user = auth()->guard('api')->user();
        $mentor = $user->mentor;
        if (!$mentor) {
            return $this->errorResponse('You must be a mentor to create a blog.', 403);
        }
        $section = Section::where('title', 'blog')->first();
        if (!$section) {
            return $this->errorResponse('Blog section not found', 404);
        }
        $data = $request->validated();


        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('blogs', 'public');
            $request->merge(['image' => $imagePath]);
        }

        // dd($request->all() , $section);
        $data['status'] = Blog::STATUS_DRAFT;
        $data['mentor_id'] = $mentor->id;
        $data['section_id'] = $section->id;

        $blog = Blog::create($data);

        $blog->load(['mentor.user', 'mentor.tracks', 'section']);
        return $this->successResponse(new BlogResource($blog), 'Blog created successfully.', 201);
    }
    // ‘show My Blogs’
    public function mentorBlogs()
    {
        $user = auth()->guard('api')->user();

        if (!$user || !$user->mentor) {
            return $this->errorResponse('You must be a mentor to view your blogs.', 403);
        }

        $per_page = request()->input('per_page', 10);

        $blogs = Blog::with(['mentor.user', 'mentor.tracks', 'section'])
            ->where('mentor_id', $user->mentor->id)
            ->where('status', Blog::STATUS_PUBLISHED)
            ->orderBy('created_at', 'desc')
            ->paginate($per_page);

        return $this->successResponse(BlogResource::collection($blogs), 'Your blogs retrieved successfully.');
    }
    // ‘update My Blogs’

    public function update(UpdateBlogRequest $request, Blog $blog)
    {
        $user = auth()->guard('api')->user();
        if (!$user || !$user->mentor) {
            return $this->errorResponse('You must be a mentor to update your blogs.', 403);
        }

        // تحقق من ملكية البلوغ
        if ($blog->mentor_id !== $user->mentor->id) {
            return $this->errorResponse('You do not have permission to update this blog.', 403);
        }

        $data = $request->validated();
        if ($request->hasFile('image')) {
            if ($blog->image && Storage::disk('public')->exists($blog->image)) {
                Storage::disk('public')->delete($blog->image);
            }
            $data['image'] = $request->file('image')->store('blogs', 'public');
        }

        $blog->update($data);
        $blog->load(['mentor.user', 'section']);

        return $this->successResponse(new BlogResource($blog), 'Blog updated successfully.', 200);
    }
    // ‘delete My Blogs’
    function destroy($slug)
    {
        $user = auth()->guard('api')->user();
        if (!$user || !$user->mentor) {
            return $this->errorResponse('You must be a mentor to delete your blogs.', 403);
        }
        $blog = Blog::where('slug', $slug)
            ->where('mentor_id', $user->mentor->id)
            ->first();
        if (!$blog) {
            return $this->errorResponse('Nothing Blog', 403);
        }

        if ($blog->image && Storage::disk('public')->exists($blog->image)) {
            Storage::disk('public')->delete($blog->image);
        }
        $blog->delete();
        return $this->successResponse(null, 'Blog deleted successfully.', 200);
    }


    public function publish($slug)
    {
        $blog = Blog::where('slug', $slug)->first();
        if (!$blog) {
            return $this->errorResponse('Blog not found', 404);
        }

        $blog->update([
            'status' => Blog::STATUS_PUBLISHED,
            'rejection_reason' => null,
        ]);

        return $this->successResponse(new BlogResource($blog), 'Blog published successfully.');
    }
    public function reject(Request $request, $slug)
    {

        $blog = Blog::where('slug', $slug)->first();
        if (!$blog) {
            return $this->errorResponse('Blog not found', 404);
        }

        $request->validate([
            'rejection_reason' => 'required|string|max:255',
        ]);

        $blog->update([
            'status' => Blog::STATUS_REJECTION,
            'rejection_reason' => $request->rejection_reason,
        ]);

        return $this->successResponse(new BlogResource($blog), 'Blog rejected successfully.');
    }
    public function draft($slug)
    {
        $blog = Blog::where('slug', $slug)->first();
        if (!$blog) {
            return $this->errorResponse('Blog not found', 404);
        }

        $blog->update([
            'status' => Blog::STATUS_DRAFT,
            'rejection_reason' => null,
        ]);
        return $this->successResponse(new BlogResource($blog), 'Blog moved to draft.');
    }
}
