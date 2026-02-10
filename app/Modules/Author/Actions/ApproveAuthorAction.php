<?php
namespace App\Modules\Author\Actions;

use App\Models\Author;
use App\Modules\Author\Events\AuthorApproved;

class ApproveAuthorAction
{
    public function execute(Author $author): Author
    {
        $author->update(['is_approved' => true]);

        event(new AuthorApproved($author));

        return $author->fresh();
    }
}
