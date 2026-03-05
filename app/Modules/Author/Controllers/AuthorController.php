<?php

namespace App\Modules\Author\Controllers;

use App\Http\Controllers\Controller;
use App\Jobs\ImportAuthorsJob;
use App\Models\Author;
use App\Modules\Author\Actions\ApproveAuthorAction;
use App\Modules\Author\Actions\CreateAuthorAction;
use App\Modules\Author\Actions\ListAuthorsAction;
use App\Modules\Author\Actions\RejectAuthorAction;
use App\Modules\Author\DTOs\AuthorData;
use App\Modules\Author\DTOs\CreateAuthorData;
use App\Modules\Author\DTOs\SearchAuthorData;
use App\Modules\Author\Requests\ImportAuthorsRequest;
use App\Modules\Author\Requests\RejectAuthorRequest;
use App\Modules\Author\Requests\SearchAuthorRequest;
use App\Modules\Author\Requests\StoreAuthorRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class AuthorController extends Controller
{
    // public function __construct()
    // {
    //     $this->authorizeResource(Author::class, 'author');
    // }


    public function index(SearchAuthorRequest $request, ListAuthorsAction $action): JsonResponse
    {
        $searchData = SearchAuthorData::fromRequest($request);

        $cacheKey = 'authors:' . md5(json_encode($searchData->toArray(), JSON_UNESCAPED_UNICODE));

        $paginator = Cache::store('redis')
            ->tags(['authors'])
            ->remember($cacheKey, now()->addMinutes(30), function () use ($action, $searchData) {
                return $action->execute($searchData);
            });

        // Transform items to DTO output
        $items = collect($paginator->items())
            ->map(fn ($author) => AuthorData::fromModel($author)->toArray())
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
                'nationality' => $searchData->nationality,
                'approved' => $searchData->approved,
                'sort_by' => $searchData->sortBy,
                'sort_order' => $searchData->sortOrder,
            ],
        ]);
    }
    public function store(StoreAuthorRequest $request, CreateAuthorAction $action): JsonResponse
    {
        $dto = CreateAuthorData::fromRequest($request);
        $author = $action->execute($dto, $request->user());

        Cache::tags(['authors'])->flush();

        return response()->json([
            'message' => 'Author created successfully',
            'data' => $author,
            'needs_approval' => $author->approval_status === '2',
        ], 201);
    }

    public function approve(Author $author, ApproveAuthorAction $action): JsonResponse
    {
        $author = $action->execute($author, request()->user());

        Cache::tags(['authors'])->flush();

        return response()->json([
            'message' => 'Author approved successfully',
            'data' => $author,
        ]);
    }

    public function reject(Author $author, RejectAuthorRequest $request, RejectAuthorAction $action): JsonResponse
    {
        $dto = RejectAuthorData::fromRequest($request);

        $author = $action->execute($author, $dto, $request->user());

        Cache::tags(['authors'])->flush();

        return response()->json([
            'message' => 'Author rejected successfully',
            'data' => $author,
        ]);
    }

    public function import(ImportAuthorsRequest $request): JsonResponse
    {
        // store file then dispatch job (chunked)
        $path = $request->file('file')->store('imports/authors');

        ImportAuthorsJob::dispatch($path, $request->user()->id);

        return response()->json([
            'message' => 'Import started',
        ], 202);
    }
}