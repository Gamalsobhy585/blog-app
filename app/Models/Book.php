<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    /** @use HasFactory<\Database\Factories\BookFactory> */
    use HasFactory;


    protected $fillable = [
        'title',
        'description',
        'isbn',
        'genre',
        'published_at',
        'total_copies',
        'available_copies',
        'price',
        'cover_image',
        'status',
        'author_id',
    ];
    protected $dates = [
        'published_at',
    ];
    protected $casts = [
        'price' => 'decimal:2',
    ];
    
    public function author()
    {
        return $this->belongsTo(Author::class);
    }
    public function borrowings()
    {
        return $this->hasMany(Borrowing::class);
    }

}
