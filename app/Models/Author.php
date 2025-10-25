<?php

namespace App\Models;
use App\Traits\HasUuid;


use Illuminate\Database\Eloquent\Model;

class Author extends Model
{
    use HasUuid;

    protected $fillable = [
        'uuid',
        'name',
        'bio',  
        'nationality',
    ];
    public function books()
    {
        return $this->hasMany(Book::class);
    }
}
