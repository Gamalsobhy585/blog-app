<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUuid;

class Member extends Model
{
    use HasUuid,HasFactory;
    

    protected $fillable = [
        'name',
        'uuid',
        'email',
        'membership_date',
        'status',
        'address',
        'phone',
    ];


    protected $casts = [
        'membership_date' => 'date',
        'status' => 'integer',

    ];  
    public function borrowings()
    {
        return $this->hasMany(Borrowing::class);
    }
    public function activeBorrowings()
    {
        return $this->borrowings()->where('status', 0);
    }
}
