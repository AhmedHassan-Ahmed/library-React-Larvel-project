<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Author extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['name', 'biography'];

    public function books() : HasMany
    {
        return $this->hasMany(Book::class);
    }

    public function scopeSearch(Builder $query, string $term): Builder
    {
        $term = mb_strtolower(trim($term));

        return $query->where(function ($q) use ($term) {
            $q->whereRaw('LOWER(name) LIKE ?', ["%{$term}%"])
              ->orWhereRaw('LOWER(biography) LIKE ?', ["%{$term}%"]);
        });
    }

    public function scopeWithBooksCount(Builder $query): Builder
    {
        return $query->withCount('books');
    }
}