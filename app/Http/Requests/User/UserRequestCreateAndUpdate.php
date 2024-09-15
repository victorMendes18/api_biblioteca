<?php

namespace App\Http\Requests\User;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Foundation\Http\FormRequest;

class UserRequestCreateAndUpdate extends FormRequest
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

        checkUserLoggedAdm($userLogged);

        return true;
    }

    public function rules()
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $this->user,
            'password' => 'required|string|min:8',
            'type' => 'required|string|in:adm,librarian'
        ];

        if ($this->isMethod('put') || $this->isMethod('patch')) {
            $rules['name'] = 'nullable|string|max:255';
            $rules['email'] = 'nullable|email|max:255|unique:users,email,' . $this->user;
            $rules['password'] = 'nullable';
            $rules['type'] = 'nullable|string|in:adm,librarian';
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'name.required' => 'The "name" field is required.',
            'name.string' => 'The "name" field must be a string.',
            'name.max' => 'The "name" field cannot be longer than 255 characters.',

            'email.required' => 'The "email" field is required.',
            'email.email' => 'The "email" field must be a valid email address.',
            'email.max' => 'The "email" field cannot be longer than 255 characters.',
            'email.unique' => 'The provided email is already in use.',

            'password.required' => 'The "password" field is required.',
            'password.string' => 'The "password" field must be a string.',
            'password.min' => 'The "password" field must be at least 8 characters.',

            'type.required' => 'The "type" field is required.',
            'type.string' => 'The "type" field must be a string.',
            'type.in' => 'The "type" field must be either "adm" or "librarian".',
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
