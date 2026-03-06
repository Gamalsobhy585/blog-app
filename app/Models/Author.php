<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;


class Author extends Model
{
    use HasUuid;
    use HasFactory;

    protected $fillable = [
        'uuid',
        'name', 
        'slug',
        'bio',
        'nationality',
        'is_approved',
        'approved_status',
        'approved_at',
        'approved_by',
        'rejected_at',
        'rejected_by',
        'created_by',
    ];

    protected $casts = [
        'is_approved' => 'boolean',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'approval_status' => 'string',

    ];

    /**
     * Scope to fetch only approved authors
     */
    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('is_approved', true);
    }

 
    public function scopePending(Builder $query): Builder
    {
        return $query->where('is_approved', false);
    }
    protected static function booted(): void
    {
        static::creating(function (Author $author) {
       

            if (empty($author->slug) && !empty($author->name)) {
                $author->slug = static::generateUniqueSlug($author->name);
            }
        });

        static::updating(function (Author $author) {

          
            if ($author->isDirty('name')) {
                $author->slug = static::generateUniqueSlug($author->name, $author->id);
            }
           
        });
    }

    private static function generateUniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name);
        $slug = $base;
        $i = 1;

        while (static::query()
            ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
            ->where('slug', $slug)
            ->exists()
        ) {
            $slug = "{$base}-{$i}";
            $i++;
        }

        return $slug;
    }
    
 
    public function books(): HasMany
    {
        return $this->hasMany(Book::class);
    }


    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

  

}