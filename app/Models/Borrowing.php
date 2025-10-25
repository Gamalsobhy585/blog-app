<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Borrowing extends Model
{
    use HasFactory;
    protected $fillable = [
        'uuid',
        'book_id',
        'member_id',
        'borrowed_date',
        'due_date',
        'return_date',
        'status',
    ];
    public function book()
    {
        return $this->belongsTo(Book::class);
    }
    public function member()
    {
        return $this->belongsTo(Member::class);
    }
    protected $casts = [
        'borrowed_date' => 'date',
        'due_date' => 'date',
        'return_date' => 'date',
        'status' => 'integer',
    
    ];


    public function isOverdue()
    {
        return $this->status === 0 && $this->due_date->isPast();
    }
    

}
