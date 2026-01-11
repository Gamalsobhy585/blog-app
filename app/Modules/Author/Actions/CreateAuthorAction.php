<?php
namespace App\Modules\Author\Actions;

use App\Models\User;
use App\Models\Author;
use Illuminate\Support\Str;
use Elastic\Elasticsearch\Client;
use Illuminate\Support\Facades\Notification;
use App\Modules\Author\DTOs\CreateAuthorData;
use App\Notifications\AuthorPendingApprovalNotification;

class CreateAuthorAction
{
    public function __construct(
        private readonly Client $client
    ) {}

    public function execute(CreateAuthorData $data, User $user): Author
    {
        $isApproved = $user->role == 1;

        $author = Author::create([
            'uuid' => (string) Str::uuid(),
            'name' => $data->name,
            'bio' => $data->bio,
            'nationality' => $data->nationality,
            'is_approved' => $isApproved,
            'created_by' => $user->id,
        ]);

        $index = config('elasticsearch.index.authors', 'authors');

        $this->client->index([
            'index' => $index,
            'id'    => (string) $author->id,
            'body'  => [
                'id'          => $author->id,
                'name'        => $author->name,
                'bio'         => $author->bio,
                'nationality' => $author->nationality,
                'is_approved' => $author->is_approved,
            ],
            'refresh' => true,
        ]);

        if (! $isApproved) {
            $admins = User::where('role', 1)->get();
            Notification::send($admins, new AuthorPendingApprovalNotification($author, $user));
        }

        return $author->fresh();
    }
}
