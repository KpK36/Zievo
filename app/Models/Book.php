<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'title',
    'author',
    'description',
    'register_by',
    'borrowed_at',
    'borrowed_by',
    'returned_at',
    'deadline',
    'notified_at'
])]
class Book extends Model
{
    use HasFactory, SoftDeletes;

    public function borrowedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'borrowed_by');
    }
}
