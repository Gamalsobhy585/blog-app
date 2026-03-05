<?php

namespace App\Jobs;

use App\Models\Author;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Str;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class BackfillAuthorSlugsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @param bool $onlyNull  true = only authors with null/empty slug, false = rebuild for all authors
     * @param int  $chunkSize how many records per chunk
     */
    public function __construct(
        public bool $onlyNull = true,
        public int $chunkSize = 500
    ) {}

    public function handle(): void
    {
        $query = Author::query()->select(['id', 'name', 'slug']);

        if ($this->onlyNull) {
            $query->where(function ($q) {
                $q->whereNull('slug')->orWhere('slug', '');
            });
        }

        // Use chunkById for safe large tables
        $query->orderBy('id')->chunkById($this->chunkSize, function ($authors) {
            foreach ($authors as $author) {
                $name = trim((string) $author->name);
                if ($name === '') {
                    continue; // skip invalid row
                }

                $base = Str::slug($name);

                // If base becomes empty (Arabic name / symbols), fallback:
                if ($base === '') {
                    $base = 'author';
                }

                // First attempt
                $slug = $base;

                // Ensure uniqueness
                $i = 1;
                while (
                    Author::query()
                        ->where('slug', $slug)
                        ->where('id', '!=', $author->id)
                        ->exists()
                ) {
                    // Stable unique variant (you can also use -{$author->id})
                    $slug = "{$base}-{$author->id}-{$i}";
                    $i++;
                }

                // Update only if needed (avoid touching timestamps too much if you want)
                if ($author->slug !== $slug) {
                    $author->slug = $slug;
                    $author->save();
                }
            }
        });
    }
}