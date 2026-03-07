<?php

namespace App\Modules\Book\Controllers;

use App\Enums\BookApprovalStatusEnum;
use App\Http\Controllers\Controller;
use App\Jobs\ImportBooksJob;
use App\Models\Book;
use App\Modules\Book\Actions\ApproveBookAction;
use App\Modules\Book\Actions\CreateBookAction;
use App\Modules\Book\Actions\ListBooksAction;
use App\Modules\Book\Actions\RejectBookAction;
use App\Modules\Book\Actions\ShowBookAction;
use App\Modules\Book\DTOs\BookData;
use App\Modules\Book\DTOs\CreateBookData;
use App\Modules\Book\DTOs\RejectBookData;
use App\Modules\Book\DTOs\SearchBookData;
use App\Modules\Book\Requests\ImportBooksRequest;
use App\Modules\Book\Requests\RejectBookRequest;
use App\Modules\Book\Requests\SearchBookRequest;
use App\Modules\Book\Requests\StoreBookRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
class BookController extends Controller
{
    public function index(SearchBookRequest $request, ListBooksAction $action): JsonResponse
    {
        $searchData = SearchBookData::fromRequest($request);

        $cacheKey = 'books:' . md5(json_encode($searchData->toArray(), JSON_UNESCAPED_UNICODE));

        $paginator = Cache::store('redis')
            ->tags(['books'])
            ->remember($cacheKey, now()->addMinutes(30), function () use ($action, $searchData) {
                return $action->execute($searchData);
            });

        $items = collect($paginator->items())
            ->map(fn (Book $book) => BookData::fromModel($book)->toArray())
            ->values();

        return response()->json([
            'data' => $items,
            'meta' => [
                'per_page' => $paginator->perPage(),
                'next_cursor' => optional($paginator->nextCursor())->encode(),
                'prev_cursor' => optional($paginator->previousCursor())->encode(),
                'has_more' => $paginator->hasMorePages(),
            ],
            'filters' => [
                'search' => $searchData->search,
                'approved' => $searchData->approved,
                'author_id' => $searchData->authorId,
                'status' => $searchData->status,
                'sort_by' => $searchData->sortBy,
                'sort_order' => $searchData->sortOrder,
            ],
        ]);
    }

    public function store(StoreBookRequest $request, CreateBookAction $action): JsonResponse
    {
        $dto = CreateBookData::fromRequest($request);
        $book = $action->execute($dto, $request->user());

        Cache::tags(['books'])->flush();

        return response()->json([
            'message' => 'Book created successfully',
            'data' => BookData::fromModel($book)->toArray(),
            'needs_approval' => (
                ($book->approval_status instanceof BookApprovalStatusEnum
                    ? $book->approval_status
                    : BookApprovalStatusEnum::from((int) $book->approval_status)
                ) === BookApprovalStatusEnum::PENDING
            ),
        ], 201);
    }

    public function approve(Book $book, ApproveBookAction $action): JsonResponse
    {
        $book = $action->execute($book, request()->user());

        Cache::tags(['books'])->flush();

        return response()->json([
            'message' => 'Book approved successfully',
            'data' => BookData::fromModel($book)->toArray(),
        ]);
    }

    public function reject(Book $book, RejectBookRequest $request, RejectBookAction $action): JsonResponse
    {
        $dto = RejectBookData::fromRequest($request);

        $book = $action->execute($book, $dto, $request->user());

        Cache::tags(['books'])->flush();

        return response()->json([
            'message' => 'Book rejected successfully',
            'data' => BookData::fromModel($book)->toArray(),
        ]);
    }

    public function import(ImportBooksRequest $request): JsonResponse
    {
        $path = $request->file('file')->store('imports/books');

        ImportBooksJob::dispatch($path, $request->user()->id);

        return response()->json([
            'message' => 'Import started successfully',
        ], 202);
    }
    public function show(Book $book, ShowBookAction $action): JsonResponse
    {
        $book = $action->execute($book);

        return response()->json([
            'data' => BookData::fromModel($book)->toArray(),
        ]);
    }

}