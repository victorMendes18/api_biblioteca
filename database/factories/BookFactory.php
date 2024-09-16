<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class BookFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'title' => $this->faker->sentence(3),
            'author' => $this->faker->name(),
            'isbn' => $this->faker->isbn13(),
            'year_of_publication' => $this->faker->year(),
            'number_of_pages' => $this->faker->numberBetween(100, 1000),
            'public' => $this->faker->boolean(),
        ];
    }
}
