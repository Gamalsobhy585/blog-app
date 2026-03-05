<?php

namespace App\Modules\Author\Actions;

use App\Models\User;
use App\Models\Author;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use App\Modules\Author\DTOs\CreateAuthorData;
use App\Notifications\AuthorPendingApprovalNotification;
use Throwable;

class CreateAuthorAction
{
    public function execute(CreateAuthorData $data, User $user): Author
    {
        try {

            return DB::transaction(function () use ($data, $user) {

                $isAdmin = $user->hasRole('admin');

                Log::info('CreateAuthorAction started', [
                    'user_id' => $user->id,
                    'is_admin' => $isAdmin,
                    'author_name' => $data->name,
                ]);

                $author = Author::create([
                    'name'            => $data->name,
                    'bio'             => $data->bio,
                    'nationality'     => $data->nationality,
                    'is_approved'     => $isAdmin ? 1 : 0,
                    'approval_status' => $isAdmin ? 1 : 2,
                    'created_by'      => $user->id,
                    'approved_by'     => $isAdmin ? $user->id : null,
                    'approved_at'     => $isAdmin ? now() : null,
                ]);

                Log::info('Author created', [
                    'author_id' => $author->id,
                    'approval_status' => $author->approval_status,
                ]);

                // Notify admins if pending
                if (! $isAdmin) {

                    $admins = User::role('admin')->get();

                    Notification::send(
                        $admins,
                        new AuthorPendingApprovalNotification($author, $user)
                    );

                    Log::info('Pending approval notification sent', [
                        'admin_count' => $admins->count(),
                        'author_id' => $author->id,
                    ]);
                }

                return $author->fresh();
            });

        } catch (Throwable $e) {

            Log::error('CreateAuthorAction failed', [
                'error_message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'user_id' => $user->id,
                'author_name' => $data->name,
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e; // Let controller handle response
        }
    }
}