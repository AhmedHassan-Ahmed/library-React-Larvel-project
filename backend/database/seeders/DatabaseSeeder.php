<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use App\Models\Author;
use App\Models\Book;
use App\Models\Member;
use App\Models\Loan;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->admin()->create([
            'name' => 'Admin User',
            'email' => 'admin@library.com',
        ]);

        $categories = Category::factory(5)->create();
        $authors = Author::factory(10)->create();

        $books = Book::factory(20)
            ->recycle($categories)
            ->recycle($authors)
            ->create();

        $members = Member::factory(10)->create();

        foreach ($members->take(5) as $member) {
            Loan::factory()->create([
                'book_id' => $books->random()->id,
                'member_id' => $member->id,
            ]);
        }
    }
}