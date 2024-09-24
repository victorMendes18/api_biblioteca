<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\UserRequestConfirmationEmail;
use App\Http\Requests\User\UserRequestCreateAndUpdate;
use App\Http\Requests\User\UserRequestGet;
use App\Http\Requests\User\UserRequestGetIdAndDelete;
use App\Http\Requests\User\UserRequestResetPassWord;
use App\Mail\SendEmailsClass;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserController extends Controller
{
    /**
     * @group Users
     * @header Authorization Bearer {token}
     * @response 200 {
     *      "message": "Users returned successfully.",
     *      "users": [
     *          {
     *              "id": 1,
     *              "name": "user",
     *              "email": "user@gmail.com",
     *              "type": "adm",
     *              "email_verified_at": "2024-09-16T18:00:16.000000Z",
     *              "avatar_url": null,
     *              "created_at": "2024-09-16T18:00:16.000000Z",
     *              "updated_at": "2024-09-16T18:00:16.000000Z",
     *              "deleted_at": null
     *          }
     *      ],
     *      "total_users": 3
     * }
     * @response 422 {
     *   "message": "Validation Error",
     *   "errors": {
     *     "email": [
     *       "The email field is required."
     *     ]
     *   }
     * }
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
                    $q->orderBy($ordering, 'desc');
                } else {
                    $q->orderBy($ordering);
                }

            });


        $page = $validated['page'] ?? 1;
        $pageSize = $validated['page_size'] ?? 50;
        $data = $query->paginate($pageSize, ['*'], 'page', $page);

        $response = [
            'message' => 'Users returned successfully.',
            'users' => $data->items(),
            'total_users'=> $data->total()
        ];

        return response()->json($response, 200);

    }

    /**
     * @group Users
     * @header Authorization Bearer {token}
     * @response 201 {
     *     "message": "User created successfully.",
     *     "user": {
     *         "name": "user",
     *         "email": "user@gmail.com",
     *         "type": "adm",
     *         "updated_at": "2024-09-22T00:30:29.000000Z",
     *         "created_at": "2024-09-22T00:30:29.000000Z",
     *         "id": 16
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
    public function store(UserRequestCreateAndUpdate $request)
    {
        $validatedData = $request->validated();

        $user = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
            'type' => $validatedData['type'],
        ]);

        //Envia email de confirmação
        $this->sendConfirmationEmail($validatedData['name'], $validatedData['email'], $validatedData['password']);

        return response()->json([
            'message' => 'User created successfully.',
            'user' => $user
        ], 201);
    }

    private function sendConfirmationEmail($nameUser, $emailUser, $passwordUser)
    {
        $credentials = [
            'email' => $emailUser,
            'password' => $passwordUser
        ];

        $token = auth('api')->attempt($credentials);

        $details = [
            'nameUser' => $nameUser,
            'token' => $token,
        ];

        Mail::to($emailUser)->send(new SendEmailsClass($details));
    }

    /**
     * @group Users
     * @header Authorization Bearer {token}
     * @response 200{
     *     "message": "User returned successfully.",
     *     "user": {
     *         "id": 3,
     *         "name": "user",
     *         "email": "user@gmail.com",
     *         "type": "librarian",
     *         "email_verified_at": "2024-09-16T15:42:16.000000Z",
     *         "avatar_url": null,
     *         "created_at": "2024-09-18T16:57:05.000000Z",
     *         "updated_at": "2024-09-18T17:01:00.000000Z",
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

    /**
     * @group Users
     * @header Authorization Bearer {token}
     * @response 200{
     *     "message": "User updated successfully.",
     *     "user": {
     *         "id": 3,
     *         "name": "user",
     *         "email": "user@gmail.com",
     *         "type": "librarian",
     *         "email_verified_at": "2024-09-16T15:42:16.000000Z",
     *         "avatar_url": null,
     *         "created_at": "2024-09-18T16:57:05.000000Z",
     *         "updated_at": "2024-09-18T17:01:00.000000Z",
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

    /**
     * @group Users
     * @header Authorization Bearer {token}
     * @response 200{
     *     "message": "User deleted successfully.",
     *     "user": {
     *         "id": 3,
     *         "name": "user",
     *         "email": "user@gmail.com",
     *         "type": "librarian",
     *         "email_verified_at": "2024-09-16T15:42:16.000000Z",
     *         "avatar_url": null,
     *         "created_at": "2024-09-18T16:57:05.000000Z",
     *         "updated_at": "2024-09-18T17:01:00.000000Z",
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
    public function destroy(UserRequestGetIdAndDelete $request, $id)
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

    /**
     * @group Users
     * @header Authorization Bearer {token}
     * @response 200 {
     *    "old_password": "testesenha",
     *    "new_password": "testesenha1",
     *    "new_password_confirmation": "testesenha1"
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
    public function resetPassword(UserRequestResetPassWord $request)
    {
        $validated = $request->validated();

        $user = auth()->user();

        if (!Hash::check($validated['old_password'], $user->password)) {
            return response()->json([
                'message' => 'The current password was entered incorrectly.',
            ], 400);
        }

        $user->update([
            'password' => Hash::make($validated['new_password'])
        ]);

        return response()->json([
            'message' => 'Password updated successfully.'
        ], 200);
    }

    /**
     * @group Users
     * @header Authorization Bearer {token}
     * @response 200 {
     *    "message": "Email confirmed successfully.",
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
    public function confirmationEmail(UserRequestConfirmationEmail $request)
    {
        $validated = $request->validated();

        $user = auth()->user();

        // Marcar o e-mail como confirmado
        $user->email_verified_at = now();
        $user->save();

        // Invalida o token atual
        JWTAuth::invalidate(JWTAuth::getToken());

        return response()->json([
            'message' => 'Email confirmed successfully.'
        ], 200);
    }
}
