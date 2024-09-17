<?php

namespace App\Http\Controllers;

use App\Http\Requests\Rent\RentRequestCreateAndUpdate;
use App\Http\Requests\Rent\RentRequestGet;
use App\Models\Book;
use App\Models\Rent;
use Illuminate\Http\Request;

class RentController extends Controller
{
    public function index(RentRequestGet $request)
    {
        $validated = $request->validated();

        $query = Rent::with(['book', 'student']);

        $query
            ->when($validated['search'] ?? null, function ($q, $search) {

                $q->whereHas('book', function ($query) use ($search) {

                    $query->where('title', 'like', "%{$search}%")
                        ->orWhere('author', 'like', "%{$search}%");

                })
                ->orWhereHas('student', function ($query) use ($search) {

                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");

                });
            })
            ->when($validated['student'] ?? null, function ($q, $student) {

                $q->whereHas('student', function ($query) use ($student) {
                    $query->where('name', $student);
                });

            })
            ->when($validated['ordering'] ?? null, function ($q, $ordering) {

                $q->orderBy($ordering, 'desc');

            });

        $page = $validated['page'] ?? 1;
        $pageSize = $validated['page_size'] ?? 50;
        $data = $query->paginate($pageSize, ['*'], 'page', $page);

        $response = [
            'message' => 'Rents returned successfully.',
            'rents' => $data->items(),
            'total_rents' => $data->total()
        ];

        return response()->json($response, 200);
    }

    public function store(RentRequestCreateAndUpdate $request)
    {
        $validated = $request->validated();

        $book = Book::find($validated['book_id']);

        if ($book->public == false && auth()->user()->type == 'librarian'){
            return response()->json([
                'message' => 'The librarian user does not have access to rent this book.',
            ], 401);
        }

        $rent = Rent::create([
            'book_id' => $validated['book_id'],
            'student_id' => $validated['student_id'],
            'delivery_date' => $validated['delivery_date'],
            'delivered' => $validated['delivered'] ?? false
        ]);

        return response()->json([
            'message' => 'Rent successfully created.',
            'rent' => $rent
        ], 201);
    }
}
