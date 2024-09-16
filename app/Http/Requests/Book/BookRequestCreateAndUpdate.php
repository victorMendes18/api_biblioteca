<?php

namespace App\Http\Requests\Book;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Foundation\Http\FormRequest;

class BookRequestCreateAndUpdate extends FormRequest
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
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'isbn' => [
                'required',
                'string',
                'max:13',
                'regex:/^(97(8|9))?\d{9}(\d|X)$/i'
            ],
            'year_of_publication' => 'required|integer|min:1000|max:' . date('Y'),
            'number_of_pages' => 'required|integer|min:1',
            'public' => 'required|boolean',
        ];

        if ($this->isMethod('put') || $this->isMethod('patch')) {
            $rules['title'] = 'nullable|string|max:255';
            $rules['author'] = 'nullable|string|max:255';
            $rules['isbn'] = [
                'nullable',
                'string',
                'max:13',
                'regex:/^(97(8|9))?\d{9}(\d|X)$/i'
            ];
            $rules['year_of_publication'] = 'nullable|integer|min:1000|max:' . date('Y');
            $rules['number_of_pages'] = 'nullable|integer|min:1';
            $rules['public'] = 'nullable|boolean';
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'title.required' => 'The "title" field is required.',
            'title.string' => 'The "title" field must be a valid string.',
            'title.max' => 'The "title" field cannot be longer than 255 characters.',

            'author.required' => 'The "author" field is required.',
            'author.string' => 'The "author" field must be a valid string.',
            'author.max' => 'The "author" field cannot be longer than 255 characters.',

            'isbn.required' => 'The "ISBN" field is required.',
            'isbn.string' => 'The "ISBN" field must be a valid string.',
            'isbn.max' => 'The "ISBN" field cannot be longer than 13 characters.',
            'isbn.regex' => 'The "ISBN" must be in a valid ISBN-10 or ISBN-13 format.',

            'year_of_publication.required' => 'The "year of publication" field is required.',
            'year_of_publication.integer' => 'The "year of publication" must be a valid year.',
            'year_of_publication.min' => 'The "year of publication" must be at least 1000.',
            'year_of_publication.max' => 'The "year of publication" cannot be in the future.',

            'number_of_pages.required' => 'The "number of pages" field is required.',
            'number_of_pages.integer' => 'The "number of pages" field must be a valid number.',
            'number_of_pages.min' => 'The "number of pages" field must be at least 1.',

            'public.required' => 'The "public" field is required.',
            'public.boolean' => 'The "public" field must be true or false.',
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
