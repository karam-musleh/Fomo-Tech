<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Http\Traits\ApiResponserTrait;
use App\Http\Resources\StudentResource;

class AdminStudentController extends Controller
{
    use ApiResponserTrait;

    public function index()
    {
        $students = User::where('role', 'student')->select("first_name", "last_name", "email", "profile_photo")->get();
        if ($students->isEmpty()) {
            return $this->errorResponse('No students found', 404);
        }
        return $this->successResponse(StudentResource::collection($students), 'Students retrieved successfully', 200);
    }

    public function show($id)
    {
        //
        $student = User::where('role', 'student')->find($id);
        if (!$student) {
            return $this->errorResponse('Student not found', 404);
        }
        return $this->successResponse(new UserResource($student), 'Student retrieved successfully', 200);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        //
        $student = User::where('role', 'student')->find($id);
        if (!$student) {
            return $this->errorResponse('Student not found', 404);
        }
        $student->delete();
        return $this->successResponse(null, 'Student deleted successfully', 200);
    }
}
