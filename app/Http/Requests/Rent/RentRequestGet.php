<?php

namespace App\Http\Requests\Rent;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Foundation\Http\FormRequest;

class RentRequestGet extends FormRequest
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

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'page' => 'nullable|integer|min:1',
            'page_size' => 'nullable|integer|min:1',
            'search' => 'nullable|string|max:255',
            'student' => 'nullable|string|exists:students,name',
            'ordering' => 'nullable|in:delivery_date,created_at',
        ];
    }

    public function messages()
    {
        return [
            'page.integer' => 'The "page" field must be an integer.',
            'page.min' => 'The "page" field must be at least 1.',

            'page_size.integer' => 'The "page_size" field must be an integer.',
            'page_size.min' => 'The "page_size" field must be at least 1.',

            'search.string' => 'The "search" field must be a string.',
            'search.max' => 'The "search" field cannot exceed 255 characters.',

            'student.string' => 'The "student" field must be a string.',
            'student.exists' => 'The selected "student" does not exist.',

            'ordering.in' => 'The "ordering" field must be either "delivery_date" or "created_at".',
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
