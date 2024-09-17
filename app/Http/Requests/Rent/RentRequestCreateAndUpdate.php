<?php

namespace App\Http\Requests\Rent;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Foundation\Http\FormRequest;

class RentRequestCreateAndUpdate extends FormRequest
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
            'book_id' => 'required|exists:books,id,deleted_at,NULL',
            'student_id' => 'required|exists:students,id,deleted_at,NULL',
            'delivery_date' => 'required|date|after:today',
            'delivered' => 'nullable|boolean',
        ];

        if ($this->isMethod('put') || $this->isMethod('patch')) {
            $rules['book_id'] = 'nullable|exists:books,id';
            $rules['student_id'] = 'nullable|exists:students,id';
            $rules['delivery_date'] = 'nullable|date|after:today';
        }

        return  $rules;
    }

    public function messages()
    {
        return [
            'book_id.required' => 'The "book" field is required.',
            'book_id.exists' => 'The selected book does not exist.',

            'student_id.required' => 'The "student" field is required.',
            'student_id.exists' => 'The selected student does not exist.',

            'delivery_date.required' => 'The "delivery date" is required.',
            'delivery_date.date' => 'The "delivery date" must be a valid date.',
            'delivery_date.after' => 'The "delivery date" must be after today.',

            'delivered.boolean' => 'The "delivered" field must be true or false.',
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
