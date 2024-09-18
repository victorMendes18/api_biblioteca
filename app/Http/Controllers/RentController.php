<?php

namespace App\Http\Controllers;

use App\Http\Requests\Rent\RentRequestCreateAndUpdate;
use App\Http\Requests\Rent\RentRequestGet;
use App\Http\Requests\Rent\RentRequestGetIdAndDelete;
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
            ->when(auth()->user()->type === 'librarian', function ($q) {

                $q->whereHas('book', function ($query) {
                    $query->where('public', true);
                });

            })
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

    public function show(RentRequestGetIdAndDelete $request, $id)
    {
        $validated = $request->validated();

        $rent = Rent::with(['book', 'student'])
            ->where('id', $id)
            ->when(auth()->user()->type === 'librarian', function ($q) {

                $q->whereHas('book', function ($query) {
                    $query->where('public', true);
                });

            })
            ->first();

        if (!$rent){
            return response()->json([
                'message' => 'Rent not found.',
            ], 404);
        }

        return response()->json([
            'message' => 'Rent returned successfully.',
            'rent' => $rent
        ], 200);
    }

    public function update(RentRequestCreateAndUpdate $request, $id)
    {

        $rent = Rent::with(['book', 'student'])->where('id', $id)->first();

        if (!$rent){
            return response()->json([
                'message' => 'Rent not found.',
            ], 404);
        }

        if ($rent->book->public == false && auth()->user()->type == 'librarian'){
            return response()->json([
                'message' => 'The librarian user does not have access to edit Private Book Rentals.',
            ], 401);
        }

        $validatedData = $request->validated();

        $rent->update([
            'delivery_date' => !empty($validatedData['delivery_date']) ? $validatedData['delivery_date'] : $rent->delivery_date,
            'delivered' => array_key_exists('delivered', $validatedData) ? $validatedData['delivered'] : $rent->delivered
        ]);

        return response()->json([
            'message' => 'Rent updated successfully.',
            'rent' => $rent
        ], 200);
    }

    public function destroy(RentRequestGetIdAndDelete $request, $id)
    {
        $rent = Rent::with(['book', 'student'])->where('id', $id)->first();

        if (!$rent){
            return response()->json([
                'message' => 'Rent not found.',
            ], 404);
        }

        if ($rent->book->public == false && auth()->user()->type == 'librarian'){
            return response()->json([
                'message' => 'The librarian user does not have access to delete Private Book Rentals.',
            ], 401);
        }

        $rent->delete();

        return response()->json([
            'message' => 'Rent deleted successfully.',
            'rent' => $rent
        ], 200);
    }
}
