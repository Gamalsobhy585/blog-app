<?php

namespace App\Models;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;
use App\Enums\BookStatusEnum;

class Book extends Model
{
    use HasUuid,HasFactory;


    protected $fillable = [
        'title',
        'description',
        'uuid',
        'isbn',
        'genre',
        'total_copies',
        'available_copies',
        'price',
        'cover_image',
        'status',
        'author_id',
        'is_approved',
        'approved_status',
        'approved_at',
        'approved_by',
        'rejected_at',
        'rejected_by',
        'created_by',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_approved' => 'boolean',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'status' => BookStatusEnum::class,
        'approval_status' => 'string',

    ];
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function author()
    {
        return $this->belongsTo(Author::class);
    }
    public function borrowings()
    {
        return $this->hasMany(Borrowing::class);
    }


    public function isAvailable(): bool
    {
        return $this->available_copies > 0
            && $this->status === BookStatusEnum::AVAILABLE;
    }

    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('is_approved', true);
    }
    public function scopePending(Builder $query): Builder
    {
        return $query->where('is_approved', false);
    }
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    protected static function booted(): void
    {
        static::creating(function (Book $book) {
            if (empty($book->slug) && !empty($book->title)) {
                $book->slug = static::generateUniqueSlug($book->title);
            }
        });

        static::updating(function (Book $book) {
            if ($book->isDirty('title')) {
                $book->slug = static::generateUniqueSlug($book->title, $book->id);
            }
        });
    }

    private static function generateUniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $base = Str::slug($title);
        $slug = $base;
        $i = 1;

        while (
            static::query()
                ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
                ->where('slug', $slug)
                ->exists()
        ) {
            $slug = "{$base}-{$i}";
            $i++;
        }

        return $slug;
    }
  

}
