<?php

namespace Database\Factories;

use App\Models\Author;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class BookFactory extends Factory
{
    public function definition(): array
    {
        return [
            'author_id' => Author::factory(),
            'category_id' => Category::factory(),
            'title' => $this->faker->sentence(3),
            'isbn' => $this->faker->unique()->isbn13(),
            'publication_year' => $this->faker->year(),
            'quantity' => 10,
            'available_quantity' => 10,
            'status' => 'available',
        ];
    }
}