<?php

namespace App\Jobs;

use App\Models\Author;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;


class ImportAuthorsJob implements ShouldQueue
{
    use Dispatchable, Queueable;

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
    $chunkSize = 3;

    $handle = fopen($fullPath, 'r');
    if (! $handle) return;

    $header = fgetcsv($handle);

    while (($line = fgetcsv($handle)) !== false) {
        $row = array_combine($header, $line);

        $name = $row['name'] ?? '';

        $rows[] = [
            'uuid'            => (string) Str::uuid(),
            'name'            => $name,
            'slug'            => $this->generateUniqueSlug($name),
            'bio'             => $row['bio'] ?? null,
            'nationality'     => $row['nationality'] ?? null,
            'is_approved'     => $isAdmin,
            'approval_status' => $isAdmin ? '1' : '2',
            'created_by'      => $user->id,
            'created_at'      => now(),
            'updated_at'      => now(),
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
}

private function generateUniqueSlug(string $name): string
{
    $base = Str::slug($name);
    $slug = $base;
    $i = 1;

    while (Author::where('slug', $slug)->exists()) {
        $slug = "{$base}-{$i}";
        $i++;
    }

    return $slug;
}

private function insertChunk(array $rows): void
{
    DB::transaction(function () use ($rows) {
        Author::insert($rows);
    });

    Log::info('ImportAuthorsJob: chunk inserted', [
        'count' => count($rows),
        'names' => array_column($rows, 'name'),
    ]);
}
 
}