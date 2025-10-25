<?php

namespace App\Models;
use App\Traits\HasUuid;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasUuid,HasFactory;


    protected $fillable = [
        'title',
        'description',
        'uuid',
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
    // public function returnBook()
    // {
    //     if($this->available_copies < $this->total_copies)
    //     {
    //         $this->available_copies += 1;
    //         $this->save();
    //     }
    // }
    // public function borrowBook()
    // {
    //     if($this->available_copies > 0)
    //     {
    //         $this->available_copies -= 1;
    //         $this->save();
    //     }
    // }

    public function isAvailable()
    {
        return $this->available_copies > 0;
    }



}
