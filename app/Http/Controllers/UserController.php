<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\UserRequestCreateAndUpdate;
use App\Http\Requests\User\UserRequestGet;
use App\Http\Requests\User\UserRequestGetIdAndDelete;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(UserRequestGet $request)
    {
        $validated = $request->validated();

        $query = User::query();

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
                    $q->orderBy($ordering, desc);
                } else {
                    $q->orderBy($ordering);
                }

            });


        $page = $validated['page'] ?? 1;
        $pageSize = $validated['page_size'] ?? 10;
        $data = $query->paginate($pageSize, ['*'], 'page', $page);

        $response = [
            'message' => 'Users returned successfully.',
            'users' => $data->items(),
            'total_users'=> $data->total()
        ];

        return response()->json($response, 200);

    }

    public function store(UserRequestCreateAndUpdate $request)
    {
        $validatedData = $request->validated();

        $user = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
            'type' => $validatedData['type'],
        ]);

        return response()->json([
            'message' => 'User created successfully.',
            'user' => $user
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(UserRequestGetIdAndDelete $request, $id)
    {
        $validated = $request->validated();

        $user = User::find($id);

        if (!$user){
            return response()->json([
                'message' => 'User not found.',
            ], 404);
        }

        return response()->json([
            'message' => 'User returned successfully.',
            'user' => $user
        ], 200);
    }


    public function update(UserRequestCreateAndUpdate $request, $id)
    {
        $user = User::find($id);

        if (!$user){
            return response()->json([
                'message' => 'User not found.',
            ], 404);
        }

        $validatedData = $request->validated();

        $user->update([
            'name' => !empty($validatedData['name']) ? $validatedData['name'] : $user->name,
            'email' => !empty($validatedData['email']) ? $validatedData['email'] : $user->email,
            'type' => !empty($validatedData['type']) ? $validatedData['type'] : $user->type,
        ]);

        return response()->json([
            'message' => 'User updated successfully.',
            'user' => $user
        ], 200);
    }

    public function destroy(UserRequestCreateAndUpdate $request, $id)
    {
        $validated = $request->validated();

        $user = User::find($id);

        if (!$user){
            return response()->json([
                'message' => 'User not found.',
            ], 404);
        }

        $user->delete();

        return response()->json([
            'message' => 'User deleted successfully.',
            'user' => $user
        ], 200);
    }
}
