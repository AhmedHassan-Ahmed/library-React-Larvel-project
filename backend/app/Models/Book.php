<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Book extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'ISBN',
        'author_id',
        'category_id',
        'publication_year', 
        'quantity',
        'available_quantity',
        'status', 
    ];

    protected $casts = [
        'quantity'           => 'integer',
        'available_quantity' => 'integer',
        'publication_year'   => 'integer',
    ];


    public function author(): BelongsTo
    {
        return $this->belongsTo(Author::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function loans(): HasMany
    {
        return $this->hasMany(Loan::class);
    }

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(Member::class, 'loans');
    }

  
    protected static function booted(): void
    {
        static::updating(function (Book $book) {
            if ($book->available_quantity === 0) {
                $book->status = 'out_of_stock';
            }
        });
    }

  
    public function isAvailableForLoan(): bool
    {
        return $this->available_quantity > 0 && $this->status === 'available';
    }

   
    public function scopeAvailable(Builder $query): Builder
    {
        return $query->where('available_quantity', '>', 0)
                     ->where('status', 'available');
    }

    public function scopeOutOfStock(Builder $query): Builder
    {
        return $query->where('available_quantity', 0);
    }

    public function scopeByStatus(Builder $query, string $status): Builder
    {
        return $query->where('status', $status);
    }

    public function scopeSearch(Builder $query, string $term): Builder
    {
        $term = mb_strtolower(trim($term));

        return $query->where(function ($q) use ($term) {
            $q->whereRaw('LOWER(title) LIKE ?', ["%{$term}%"])
              ->orWhereRaw('LOWER(ISBN) LIKE ?', ["%{$term}%"])
              ->orWhereHas('author', fn($a) => $a->whereRaw('LOWER(name) LIKE ?', ["%{$term}%"]))
              ->orWhereHas('category', fn($c) => $c->whereRaw('LOWER(name) LIKE ?', ["%{$term}%"]));
        });
    } 

    public function scopeNeedsRestock(Builder $query, int $threshold = 1): Builder
    {
        return $query->where('available_quantity', '<=', $threshold);
    }
}