<?php

namespace App\Http\Requests\Student;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Foundation\Http\FormRequest;

class StudentRequestCreateAndUpdate extends FormRequest
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
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:students,email,' . $this->student,
            'phone' => 'nullable|string|min:8',
            'address' => 'nullable|string|max:255',
        ];

        if ($this->isMethod('put') || $this->isMethod('patch')) {
            $rules['name'] = 'nullable|string|max:255';
            $rules['email'] = 'nullable|email|max:255|unique:students,email,' . $this->student;
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'name.required' => 'The "name" field is required.',
            'name.string' => 'The "name" field must be a string.',
            'name.max' => 'The "name" field may not be greater than 255 characters.',

            'email.required' => 'The "email" field is required.',
            'email.email' => 'The "email" field must be a valid email address.',
            'email.max' => 'The "email" field may not be greater than 255 characters.',
            'email.unique' => 'The email has already been taken.',

            'phone.string' => 'The "phone" field must be a string.',
            'phone.min' => 'The "phone" field must be at least 8 characters.',

            'address.string' => 'The "address" field must be a string.',
            'address.max' => 'The "address" field may not be greater than 255 characters.',
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
