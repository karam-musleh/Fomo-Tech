<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\Section;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\SectionRequest;
use App\Http\Traits\ApiResponserTrait;
use App\Http\Resources\SectionResource;
use Illuminate\Support\Facades\Storage;

class AdminSectionController extends Controller
{
    use ApiResponserTrait;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $perPage = request()->input('per_page', 5);
        $sections = Section::select('id', 'title', 'image', 'status')->paginate($perPage);
        if (!$sections) {
            return $this->errorResponse('No sections found', 404);
        }
        return $this->successResponse(SectionResource::collection($sections), 'Sections retrieved successfully.');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(SectionRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('sections', 'public');
        }

        $data['status'] = Section::STATUS_DRAFT;
        // dd($data);
        $section = Section::create($data);

        return $this->successResponse(
            new SectionResource($section),
            'Section created successfully.'

        );
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
    public function update(SectionRequest $request, $slug)
    {
        $section = Section::where('slug', $slug)->first();

        if (!$section) {
            return $this->errorResponse('Section not found', 404);
        }

        $data = $request->validated();

        if ($request->hasFile('image')) {
            if ($section->image) {
                Storage::disk('public')->delete($section->image);
            }

            $imagePath = $request->file('image')->store('sections', 'public');
            $data['image'] = $imagePath;
        }

        $section->update($data);

        return $this->successResponse(
            new SectionResource($section),
            'Section updated successfully.'
        );
    }

    /**
     * Remove the specified resource from storage.
     */

    public function destroy($slug)
    {
        $section = Section::where('slug', $slug)->first();

        if (!$section) {
            return $this->errorResponse('Section not found', 404);
        }

        // حذف الصورة القديمة إذا موجودة
        if ($section->image) {
            Storage::disk('public')->delete($section->image);
        }

        // حذف القسم نفسه
        $section->delete();

        return $this->successResponse(
            null,
            'Section deleted successfully.'
        );
    }

    // تحديث الحالة إلى Draft
    public function setDraft($slug)
    {
        $section = Section::where('slug', $slug)->first();

        if (!$section) {
            return $this->errorResponse('Section not found', 404);
        }

        $section->status = Section::STATUS_DRAFT; // تأكد أن عندك هذا الثابت في الموديل
        $section->save();

        return $this->successResponse(
            new SectionResource($section),
            'Section set to draft successfully.'
        );
    }

    // تحديث الحالة إلى Published
    public function setPublished($slug)
    {
        $section = Section::where('slug', $slug)->first();

        if (!$section) {
            return $this->errorResponse('Section not found', 404);
        }

        $section->status = Section::STATUS_PUBLISHED; // تأكد أن عندك هذا الثابت في الموديل
        $section->save();

        return $this->successResponse(
            new SectionResource($section),
            'Section published successfully.'
        );
    }
}
