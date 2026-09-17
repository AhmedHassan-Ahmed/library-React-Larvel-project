<?php

namespace Database\Factories;

use App\Models\Book;
use App\Models\Loan;
use App\Models\Member;
use Illuminate\Database\Eloquent\Factories\Factory;

class LoanFactory extends Factory
{
    protected $model = Loan::class;

    public function definition(): array
    {
        $borrowedAt = $this->faker->dateTimeBetween('-1 month', 'now');
        $dueAt = (clone $borrowedAt)->modify('+14 days');

        return [
            'book_id' => Book::factory(),
            'member_id' => Member::factory(),
            'status' => 'borrowed',
            'borrowed_at' => $borrowedAt,
            'due_at' => $dueAt,
            'returned_at' => null,
            'fine_amount' => 0.00,
        ];
    }
}