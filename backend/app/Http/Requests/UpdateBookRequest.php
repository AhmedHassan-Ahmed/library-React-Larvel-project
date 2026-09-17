<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateBookRequest extends FormRequest
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
        'title' => 'sometimes|required|string|max:255',
        'ISBN' => 'sometimes|required|string|unique:books,ISBN,' . $this->route('book'),
        'author_id' => 'sometimes|required|exists:authors,id',
        'category_id' => 'sometimes|required|exists:categories,id',
        'quantity' => 'sometimes|required|integer|min:0',
        'available_quantity' => 'sometimes|required|integer|min:0',
        'status' => 'sometimes|required|in:available,borrowed,reserved',
        'published_year' => 'nullable|integer|digits:4',
    ];
}
}
