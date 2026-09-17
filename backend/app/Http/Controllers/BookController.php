<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Http\Requests\StoreBookRequest;
use App\Http\Requests\UpdateBookRequest;
use App\Http\Resources\BookResource;

class BookController extends Controller
{
    public function index()
    {
        $books = Book::with(['author', 'category'])->paginate(10);
        return BookResource::collection($books);
    }

    public function store(StoreBookRequest $request)
    {
        $book = Book::create($request->validated());
        return new BookResource($book->load(['author', 'category']));
    }

    public function show(Book $book)
    {
        return new BookResource($book->load(['author', 'category']));
    }

    public function update(UpdateBookRequest $request, Book $book)
    {
        $book->update($request->validated());
        return new BookResource($book->load(['author', 'category']));
    }

    public function destroy(Book $book)
    {
        $book->delete(); 
        return response()->json(['message' => 'Book deleted successfully'], 200);
    }
}