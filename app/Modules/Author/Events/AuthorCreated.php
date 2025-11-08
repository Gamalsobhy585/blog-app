<?php

// AuthorCreated.php
namespace App\Modules\Author\Events;

use App\Models\Author;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AuthorCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Author $author)
    {
    }
}