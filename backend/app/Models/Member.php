<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Member extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'status',
        'membership_expiration_date',
    ];

    protected $casts = [
        'membership_expiration_date' => 'date',
    ];

    public function loans(): HasMany
    {
        return $this->hasMany(Loan::class);
    }

    public function books(): BelongsToMany
    {
        return $this->belongsToMany(Book::class, 'loans')
                    ->withPivot('borrowed_at', 'due_date', 'returned_at', 'status')
                    ->withTimestamps();
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

    public function scopeExpired(Builder $query): Builder
    {
        return $query->where('membership_expiration_date', '<', now());
    }

    public function scopeSearch(Builder $query, string $term): Builder
    {
        $term = mb_strtolower(trim($term));

        return $query->where(function ($q) use ($term) {
            $q->whereRaw('LOWER(name) LIKE ?', ["%{$term}%"])
              ->orWhereRaw('LOWER(email) LIKE ?', ["%{$term}%"])
              ->orWhereRaw('LOWER(phone) LIKE ?', ["%{$term}%"]);
        });
    }

    public function canBorrow(): bool
    {
        return $this->status === 'active' && 
               ($this->membership_expiration_date === null || $this->membership_expiration_date->isFuture());
    }
}