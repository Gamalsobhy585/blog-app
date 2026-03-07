<?php

namespace App\Jobs;

use App\Models\Book;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Enums\BookStatusEnum;
use App\Enums\BookApprovalStatusEnum;

class ImportBooksJob implements ShouldQueue
{
    use Dispatchable, Queueable, SerializesModels;

    public function __construct(
        public string $path,
        public int $userId
    ) {}

    public function handle(): void
    {
        $user = User::findOrFail($this->userId);
        $isAdmin = $user->hasRole('admin');

        $fullPath = Storage::path($this->path);

        $rows = [];
        $chunkSize = 100;

        $handle = fopen($fullPath, 'r');

        if (! $handle) {
            Log::error('ImportBooksJob failed to open file', [
                'path' => $this->path,
                'user_id' => $this->userId,
            ]);
            return;
        }

        $header = fgetcsv($handle);

        if (! $header) {
            fclose($handle);
            Storage::delete($this->path);

            Log::warning('ImportBooksJob empty or invalid CSV header', [
                'path' => $this->path,
                'user_id' => $this->userId,
            ]);
            return;
        }

        while (($line = fgetcsv($handle)) !== false) {
            $row = array_combine($header, $line);

            if (! $row) {
                continue;
            }

            $title = trim((string) ($row['title'] ?? ''));

            if ($title === '') {
                continue;
            }

            $totalCopies = (int) ($row['total_copies'] ?? 0);
            $availableCopies = (int) ($row['available_copies'] ?? 0);

            $rows[] = [
                'uuid' => (string) Str::uuid(),
                'title' => $title,
                'description' => $row['description'] ?? null,
                'slug' => $this->generateUniqueSlug($title),
                'total_copies' => $totalCopies,
                'available_copies' => $availableCopies,
                'is_approved' => $isAdmin,
                'approval_status' => $isAdmin
                    ? BookApprovalStatusEnum::APPROVED->value
                    : BookApprovalStatusEnum::PENDING->value,
                'cover_image' => $row['cover_image'] ?? null,
                'price' => isset($row['price']) && $row['price'] !== '' ? $row['price'] : null,
                'status' => $this->resolveStatus($row, $availableCopies),
                'author_id' => isset($row['author_id']) && $row['author_id'] !== ''
                    ? (int) $row['author_id']
                    : null,
                'created_by' => $user->id,
                'approved_by' => $isAdmin ? $user->id : null,
                'approved_at' => $isAdmin ? now() : null,
                'rejected_at' => null,
                'rejection_reason' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            if (count($rows) >= $chunkSize) {
                $this->insertChunk($rows);
                $rows = [];
            }
        }

        if (! empty($rows)) {
            $this->insertChunk($rows);
        }

        fclose($handle);
        Storage::delete($this->path);

        Cache::tags(['books'])->flush();

        Log::info('ImportBooksJob completed successfully', [
            'path' => $this->path,
            'user_id' => $this->userId,
        ]);
    }

    private function generateUniqueSlug(string $title): string
    {
        $base = Str::slug($title);
        $slug = $base !== '' ? $base : 'book';
        $i = 1;

        while (Book::where('slug', $slug)->exists()) {
            $slug = "{$base}-{$i}";
            $i++;
        }

        return $slug;
    }

    private function resolveStatus(array $row, int $availableCopies): int
    {
        if (isset($row['status']) && $row['status'] !== '') {
            return (int) $row['status'];
        }

        return $availableCopies > 0
            ? BookStatusEnum::AVAILABLE->value
            : BookStatusEnum::UNAVAILABLE->value;
    }

    private function insertChunk(array $rows): void
    {
        DB::transaction(function () use ($rows) {
            Book::insert($rows);
        });
    }
}