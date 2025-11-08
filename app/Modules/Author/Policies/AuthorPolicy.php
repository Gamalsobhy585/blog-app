<?php

namespace App\Modules\Author\Policies;

use App\Models\Author;
use App\Models\User;

class AuthorPolicy
{
 
    public function viewAny(User $user): bool
    {
        return true; // All authenticated users can view authors
    }

 
    public function view(User $user, Author $author): bool
    {
        return true; 
    }


    public function create(User $user): bool
    {
        return in_array($user->role, [1, 2]);
    }


    public function update(User $user, Author $author): bool
    {
        if ($user->role === 1) {
            return true;
        }

        if ($user->role === 2 && $author->created_by === $user->id) {
            return true;
        }

        return false;
    }


    public function delete(User $user, Author $author): bool
    {
        return $user->role === 1;
    }


    public function approve(User $user, Author $author): bool
    {
        return $user->role === 1 && !$author->is_approved;
    }


}