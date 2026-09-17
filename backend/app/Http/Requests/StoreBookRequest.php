<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreBookRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
   public function rules(): array
{
    return [
        'title' => 'required|string|max:255',
        'ISBN' => 'required|string|unique:books,ISBN',
        'author_id' => 'required|exists:authors,id',
        'category_id' => 'required|exists:categories,id',
        'quantity' => 'required|integer|min:0',
        'available_quantity' => 'required|integer|min:0',
        'status' => 'required|in:available,borrowed,reserved', // عدلي القيم حسب الـ enum عندك
        'published_year' => 'nullable|integer|digits:4',
    ];
}
}
