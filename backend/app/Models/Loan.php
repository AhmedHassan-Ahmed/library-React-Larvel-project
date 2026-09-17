<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Loan extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'book_id',
        'member_id',
        'status', // e.g., 'borrowed', 'returned', 'overdue'
        'borrowed_at',
        'due_at',
        'returned_at',
        'fine_amount',
    ];

    protected $casts = [
        'borrowed_at' => 'datetime',
        'due_at' => 'datetime',
        'returned_at' => 'datetime',
        'fine_amount' => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::creating(function (Loan $loan) {
            $loan->borrowed_at = $loan->borrowed_at ?? now();
            $loan->due_at = $loan->due_at ?? now()->addDays(14);
        });
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'borrowed');
    }

    public function scopeOverdue(Builder $query): Builder
    {
        return $query->where('status', 'borrowed')
                     ->where('due_at', '<', now());
    }

    public function scopeReturned(Builder $query): Builder
    {
        return $query->where('status', 'returned');
    }

    public function isOverdue(): bool
    {
        return $this->status === 'borrowed' && Carbon::now()->greaterThan($this->due_at);
    }

    public function calculateFine(float $dailyRate = 5.0): float
    {
        if (!$this->isOverdue()) {
            return 0.0;
        }

        $daysLate = now()->diffInDays($this->due_at);
        return $daysLate * $dailyRate;
    }
}