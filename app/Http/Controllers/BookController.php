<?php

namespace App\Http\Controllers;

use App\Http\Requests\Book\BookRequestCreateAndUpdate;
use App\Http\Requests\Book\BookRequestGet;
use App\Http\Requests\Book\BookRequestGetIdAndDelete;
use App\Models\Book;
use Illuminate\Http\Request;

class BookController extends Controller
{
    /**
     * @group Books
     * @header Authorization Bearer {token}
     * @response 200 {
     *     "message": "Books returned successfully.",
     *     "books": [
     *         {
     *             "id": 1,
     *             "title": "Book",
     *             "author": "User",
     *             "isbn": "9792132370946",
     *             "year_of_publication": 2002,
     *             "number_of_pages": 123,
     *             "public": 0,
     *             "created_at": "2024-09-16T18:00:16.000000Z",
     *             "updated_at": "2024-09-18T17:07:49.000000Z",
     *             "deleted_at": null
     *         }
     *     ],
     *     "total_books": 18
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
    public function index(BookRequestGet $request)
    {
        $validated = $request->validated();

        $query = Book::query();

        $query
            ->when(auth()->user()->type == 'librarian', function ($q) {

                $q->where('public', true);

            })
            ->when($validated['search'] ?? null, function ($q, $search) {

                $q->where(function ($query) use ($search) {
                    $query->where('title', 'like', "%{$search}%")
                        ->orWhere('author', 'like', "%{$search}%")
                        ->orWhere('year_of_publication', 'like', "%{$search}%");
                });

            })
            ->when($validated['isbn'] ?? null, function ($q, $isbn) {

                $q->where('isbn', $isbn);

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
            'message' => 'Books returned successfully.',
            'books' => $data->items(),
            'total_books'=> $data->total()
        ];

        return response()->json($response, 200);
    }

    /**
     * @group Books
     * @header Authorization Bearer {token}
     * @response 201 {
     *     "message": "Book created successfully.",
     *     "book": {
     *         "title": "Book",
     *         "author": "User",
     *         "isbn": "9782932327286",
     *         "year_of_publication": 2012,
     *         "number_of_pages": 1256,
     *         "public": true,
     *         "updated_at": "2024-09-22T01:24:18.000000Z",
     *         "created_at": "2024-09-22T01:24:18.000000Z",
     *         "id": 23
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
    public function store(BookRequestCreateAndUpdate $request)
    {
        $validatedData = $request->validated();

        $book = Book::create([
            'title' => $validatedData['title'],
            'author' => $validatedData['author'],
            'isbn' => $validatedData['isbn'],
            'year_of_publication' => $validatedData['year_of_publication'],
            'number_of_pages' => $validatedData['number_of_pages'],
            'public' => $validatedData['public']
        ]);

        return response()->json([
            'message' => 'Book created successfully.',
            'book' => $book
        ], 201);
    }

    /**
     * @group Books
     * @header Authorization Bearer {token}
     * @response 200 {
     *     "message": "Book returned successfully.",
     *     "book": {
     *         "id": 1,
     *         "title": "Book",
     *         "author": "User",
     *         "isbn": "9782932327286",
     *         "year_of_publication": 2012,
     *         "number_of_pages": 1256,
     *         "public": true,
     *         "updated_at": "2024-09-22T01:24:18.000000Z",
     *         "created_at": "2024-09-22T01:24:18.000000Z",
     *         "id": 23
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
    public function show(BookRequestGetIdAndDelete $request,$id)
    {
        $validated = $request->validated();

        $book = Book::query()
            ->where('id', $id)
            ->when(auth()->user()->type == 'librarian', function ($q) {

                $q->where('public', true);

            })->first();

        if (!$book){
            return response()->json([
                'message' => 'Book not found.',
            ], 404);
        }

        return response()->json([
            'message' => 'book returned successfully.',
            'book' => $book
        ], 200);
    }

    /**
     * @group Books
     * @header Authorization Bearer {token}
     * @response 200 {
     *     "message": "Book updated successfully.",
     *     "book": {
     *         "id": 1,
     *         "title": "Book",
     *         "author": "User",
     *         "isbn": "9782932327286",
     *         "year_of_publication": 2012,
     *         "number_of_pages": 1256,
     *         "public": true,
     *         "updated_at": "2024-09-22T01:24:18.000000Z",
     *         "created_at": "2024-09-22T01:24:18.000000Z",
     *         "id": 23
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
    public function update(BookRequestCreateAndUpdate $request, $id)
    {
        $book = Book::find($id);

        if (!$book){
            return response()->json([
                'message' => 'Book not found.',
            ], 404);
        }

        $validatedData = $request->validated();

        $book->update([
            'title' => !empty($validatedData['title']) ? $validatedData['title'] : $book->title,
            'author' => !empty($validatedData['author']) ? $validatedData['author'] : $book->author,
            'isbn' => !empty($validatedData['isbn']) ? $validatedData['isbn'] : $book->isbn,
            'year_of_publication' => !empty($validatedData['year_of_publication']) ? $validatedData['year_of_publication'] : $book->year_of_publication,
            'number_of_pages' => !empty($validatedData['number_of_pages']) ? $validatedData['number_of_pages'] : $book->number_of_pages,
            'public' => array_key_exists('public', $validatedData) ? $validatedData['public'] : $book->public
        ]);

        return response()->json([
            'message' => 'Book updated successfully.',
            'Book' => $book
        ], 200);
    }

    /**
     * @group Books
     * @header Authorization Bearer {token}
     * @response 200 {
     *     "message": "Book deleted successfully.",
     *     "book": {
     *         "id": 1,
     *         "title": "Book",
     *         "author": "User",
     *         "isbn": "9782932327286",
     *         "year_of_publication": 2012,
     *         "number_of_pages": 1256,
     *         "public": true,
     *         "updated_at": "2024-09-22T01:24:18.000000Z",
     *         "created_at": "2024-09-22T01:24:18.000000Z",
     *         "id": 23
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
    public function destroy(BookRequestGetIdAndDelete $request, $id)
    {
        $validated = $request->validated();

        $book = Book::find($id);

        if (!$book){
            return response()->json([
                'message' => 'Book not found.',
            ], 404);
        }

        if ($book->rents()->where('delivered', false)->count()){
            return response()->json([
                'message' => 'Book cannot be deleted because it has open rentals.',
            ], 400);
        }

        $book->delete();

        return response()->json([
            'message' => 'Book deleted successfully.',
            'book' => $book
        ], 200);
    }
}
