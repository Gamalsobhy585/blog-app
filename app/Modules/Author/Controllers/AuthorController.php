<?php

namespace App\Modules\Author\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Author\Actions\CreateAuthorAction;
use App\Modules\Author\Actions\UpdateAuthorAction;
use App\Modules\Author\Actions\DeleteAuthorAction;
use App\Modules\Author\Actions\ListAuthorsAction;
use App\Modules\Author\Actions\ApproveAuthorAction;
use App\Modules\Author\DTOs\CreateAuthorData;
use App\Modules\Author\DTOs\UpdateAuthorData;
use App\Modules\Author\DTOs\SearchAuthorData;
use App\Modules\Author\Requests\StoreAuthorRequest;
use App\Modules\Author\Requests\UpdateAuthorRequest;
use App\Modules\Author\Requests\SearchAuthorRequest;
use App\Models\Author;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Throwable;

class AuthorController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Author::class, 'author');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(SearchAuthorRequest $request, ListAuthorsAction $action): JsonResponse
    {
        $searchData = SearchAuthorData::fromRequest($request);
        
        $cacheKey = 'authors_' . md5(serialize($searchData->toArray()));
        
        $authors = Cache::tags(['authors'])->remember($cacheKey, 3600, function () use ($action, $searchData) {
            return $action->execute($searchData);
        });

        return response()->json($authors);
    }
    public function store(StoreAuthorRequest $request, CreateAuthorAction $action): JsonResponse
    {
        try {
        $authorData = CreateAuthorData::fromRequest($request);
        $author = $action->execute($authorData, $request->user());
        Cache::tags(['authors'])->flush();

        return response()->json([
            'message' => 'Author created successfully',
            'data' => $author,
            'needs_approval' => !$author->is_approved
        ], 201);

        } catch (Throwable $e) {
            report($e);

            return response()->json([
                'message' => 'Something went wrong.',
                'error'   => $e->getMessage(), // ممكن تشيلها في الإنتاج
            ], 500);
            }
    }
 


    /**
     * Approve a pending author (admin only)
     */
    public function approve(Author $author, ApproveAuthorAction $action): JsonResponse
    {
        $this->authorize('approve', $author);

        $approvedAuthor = $action->execute($author);

        Cache::tags(['authors'])->flush();

        return response()->json([
            'message' => 'Author approved successfully',
            'data' => $approvedAuthor
        ]);
    }
}