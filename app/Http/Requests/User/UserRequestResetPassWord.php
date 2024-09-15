<?php

namespace App\Http\Requests\User;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Foundation\Http\FormRequest;

class UserRequestResetPassWord extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $userLogged = auth()->user();

        checkUserLoggedVerified($userLogged);

        return true;
    }

    public function rules()
    {
        return [
            'old_password' => 'required|string|min:8',
            'new_password' => 'required|string|min:8|confirmed',
        ];
    }

    public function messages()
{
    return [
        'old_password.required' => 'The old password field is required.',
        'old_password.string' => 'The old password must be a valid string.',
        'old_password.min' => 'The old password must be at least 8 characters long.',

        'new_password.required' => 'The new password field is required.',
        'new_password.string' => 'The new password must be a valid string.',
        'new_password.min' => 'The new password must be at least 8 characters long.',
        'new_password.confirmed' => 'The new password confirmation does not match the new password.',
    ];
}

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'message' => 'The data provided was invalid.',
                'errors' => $validator->errors(),
            ], 422)
        );
    }
}
