<?php

namespace App\Jobs;

use App\Models\Book;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Str;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class BackfillBookSlugsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @param bool $onlyNull  true = only books with null/empty slug, false = rebuild for all books
     * @param int  $chunkSize how many records per chunk
     */
    public function __construct(
        public bool $onlyNull = true,
        public int $chunkSize = 500
    ) {}

    public function handle(): void
    {
        $query = Book::query()->select(['id', 'name', 'slug']);

        if ($this->onlyNull) {
            $query->where(function ($q) {
                $q->whereNull('slug')->orWhere('slug', '');
            });
        }

        // Use chunkById for safe large tables
        $query->orderBy('id')->chunkById($this->chunkSize, function ($books) {
            foreach ($books as $book) {
                $name = trim((string) $book->name);
                if ($name === '') {
                    continue; // skip invalid row
                }

                $base = Str::slug($name);

                // If base becomes empty (Arabic name / symbols), fallback:
                if ($base === '') {
                    $base = 'book';
                }

                // First attempt
                $slug = $base;

                // Ensure uniqueness
                $i = 1;
                while (
                    Book::query()
                        ->where('slug', $slug)
                        ->where('id', '!=', $book->id)
                        ->exists()
                ) {
                    // Stable unique variant (you can also use -{$book->id})
                    $slug = "{$base}-{$book->id}-{$i}";
                    $i++;
                }

                // Update only if needed (avoid touching timestamps too much if you want)
                if ($book->slug !== $slug) {
                    $book->slug = $slug;
                    $book->save();
                }
            }
        });
    }
}