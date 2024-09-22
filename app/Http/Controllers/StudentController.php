<?php

namespace App\Http\Controllers;

use App\Http\Requests\Student\StudentRequestCreateAndUpdate;
use App\Http\Requests\Student\StudentRequestGet;
use App\Http\Requests\Student\StudentRequestGetIdAndDelete;
use App\Models\Student;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    /**
     * @group Students
     * @header Authorization Bearer {token}
     * @response 200 {
     *     "message": "Students returned successfully.",
     *     "students": [
     *         {
     *             "id": 1,
     *             "name": "Student",
     *             "email": "Student@gmail.com",
     *             "phone": "5587999999999",
     *             "address": "address",
     *             "created_at": "2024-09-16T19:47:50.000000Z",
     *             "updated_at": "2024-09-16T19:47:50.000000Z",
     *             "deleted_at": null
     *         }
     *     ],
     *     "total_students": 12
     * }
     * @response 422 {
     *   "message": "Validation Error",
     *   "errors": {
     *     "email": [
     *       "The email field is required."
     *     ]
     *   }
     * }
     * */
    public function index(StudentRequestGet $request)
    {
        $validated = $request->validated();

        $query = Student::query();

        $query
            ->when($validated['search'] ?? null, function ($q, $search) {

                $q->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });

            })
            ->when($validated['email'] ?? null, function ($q, $email) {

                $q->where('email', $email);

            })
            ->when($validated['ordering'] ?? null, function ($q, $ordering) {

                if ($ordering == 'created_at'){
                    $q->orderBy($ordering, 'desc');
                } else {
                    $q->orderBy($ordering);
                }

            });


        $page = $validated['page'] ?? 1;
        $pageSize = $validated['page_size'] ?? 50;
        $data = $query->paginate($pageSize, ['*'], 'page', $page);

        $response = [
            'message' => 'Students returned successfully.',
            'students' => $data->items(),
            'total_students'=> $data->total()
        ];

        return response()->json($response, 200);
    }

    /**
     * @group Students
     * @header Authorization Bearer {token}
     * @response 201 {
     *     "message": "Student created successfully.",
     *     "student": {
     *         "name": "student",
     *         "email": "student@gmail.com",
     *         "phone": "5587999999999",
     *         "address": "address",
     *         "created_at": "2024-09-22T01:35:48.000000Z",
     *         "updated_at": "2024-09-22T01:35:48.000000Z",
     *         "id": 22
     *     }
     * }
     * @response 422 {
     *   "message": "Validation Error",
     *   "errors": {
     *     "email": [
     *       "The email field is required."
     *     ]
     *   }
     * }
     * */
    public function store(StudentRequestCreateAndUpdate $request)
    {
        $validatedData = $request->validated();

        $student = Student::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'phone' => !empty($validatedData['phone']) ? $validatedData['phone'] : null,
            'address' => !empty($validatedData['address']) ? $validatedData['address'] : null,
        ]);

        return response()->json([
            'message' => 'Student created successfully.',
            'student' => $student
        ], 201);
    }

    /**
     * @group Students
     * @header Authorization Bearer {token}
     * @response 200 {
     *     "message": "Student returned successfully.",
     *     "student": {
     *         "id": 11,
     *         "name": "student",
     *         "email": "student@gmail.com",
     *         "phone": "5587999999999",
     *         "address": "address",
     *         "created_at": "2024-09-22T01:35:48.000000Z",
     *         "updated_at": "2024-09-22T01:35:48.000000Z",
     *         "deleted_at": null
     *     }
     * }
     * @response 422 {
     *   "message": "Validation Error",
     *   "errors": {
     *     "email": [
     *       "The email field is required."
     *     ]
     *   }
     * }
     * */
    public function show(StudentRequestGetIdAndDelete $request, $id)
    {
        $validated = $request->validated();

        $student = Student::find($id);

        if (!$student){
            return response()->json([
                'message' => 'Student not found.',
            ], 404);
        }

        return response()->json([
            'message' => 'Student returned successfully.',
            'student' => $student
        ], 200);
    }

    /**
     * @group Students
     * @header Authorization Bearer {token}
     * @response 200 {
     *     "message": "Student updated successfully.",
     *     "student": {
     *         "id": 11,
     *         "name": "student",
     *         "email": "student@gmail.com",
     *         "phone": "5587999999999",
     *         "address": "address",
     *         "created_at": "2024-09-22T01:35:48.000000Z",
     *         "updated_at": "2024-09-22T01:35:48.000000Z",
     *         "deleted_at": null
     *     }
     * }
     * @response 422 {
     *   "message": "Validation Error",
     *   "errors": {
     *     "email": [
     *       "The email field is required."
     *     ]
     *   }
     * }
     * */
    public function update(StudentRequestCreateAndUpdate $request, $id)
    {
        $student = Student::find($id);

        if (!$student){
            return response()->json([
                'message' => 'Student not found.',
            ], 404);
        }

        $validatedData = $request->validated();

        $student->update([
            'name' => !empty($validatedData['name']) ? $validatedData['name'] : $student->name,
            'email' => !empty($validatedData['email']) ? $validatedData['email'] : $student->email,
            'phone' => !empty($validatedData['phone']) ? $validatedData['phone'] : $student->phone,
            'address' => !empty($validatedData['address']) ? $validatedData['address'] : $student->address
        ]);

        return response()->json([
            'message' => 'Student updated successfully.',
            'student' => $student
        ], 200);
    }

    /**
     * @group Students
     * @header Authorization Bearer {token}
     * @response 200 {
     *     "message": "Student deleted successfully.",
     *     "student": {
     *         "id": 11,
     *         "name": "student",
     *         "email": "student@gmail.com",
     *         "phone": "5587999999999",
     *         "address": "address",
     *         "created_at": "2024-09-22T01:35:48.000000Z",
     *         "updated_at": "2024-09-22T01:35:48.000000Z",
     *         "deleted_at": null
     *     }
     * }
     * @response 422 {
     *   "message": "Validation Error",
     *   "errors": {
     *     "email": [
     *       "The email field is required."
     *     ]
     *   }
     * }
     * */
    public function destroy(StudentRequestGetIdAndDelete $request, $id)
    {
        $validated = $request->validated();

        $student = Student::find($id);

        if (!$student){
            return response()->json([
                'message' => 'Student not found.',
            ], 404);
        }

        if ($student->rents()->where('delivered', false)->count()){
            return response()->json([
                'message' => 'Student cannot be deleted because it has open rentals.',
            ], 400);
        }

        $student->delete();

        return response()->json([
            'message' => 'Student deleted successfully.',
            'student' => $student
        ], 200);
    }
}
