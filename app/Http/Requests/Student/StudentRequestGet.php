<?php

namespace App\Http\Requests\Student;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Foundation\Http\FormRequest;

class StudentRequestGet extends FormRequest
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
            'page' => 'nullable|integer|min:1',
            'page_size' => 'nullable|integer|min:1',
            'search' => 'nullable|string',
            'email' => 'nullable|email',
            'ordering' => 'nullable|in:name,created_at'
        ];
    }

    public function messages()
    {
        return [
            'page.integer' => 'The "page" field must be an integer.',
            'page.min' => 'The minimum value for the "page" field is 1.',

            'page_size.integer' => 'The "page_size" field must be an integer.',
            'page_size.min' => 'The minimum value for the "page_size" field is 1.',

            'search.string' => 'The "search" field must be a string.',

            'email.email' => 'The "email" field must be a valid email address.',

            'ordering.in' => 'The "ordering" field must be either "name" or "created_at".',
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
