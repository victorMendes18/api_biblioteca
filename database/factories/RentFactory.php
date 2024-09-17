<?php

namespace Database\Factories;

use App\Models\Book;
use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class RentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'book_id' => Book::factory(),
            'student_id' => Student::factory(),
            'delivery_date' => Carbon::now()->addDays(rand(1, 30)),
            'delivered' => $this->faker->boolean(),
        ];
    }
}
