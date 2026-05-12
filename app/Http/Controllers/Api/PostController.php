<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\DataTransferObjects\PostDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Models\Post;
use App\Services\PostService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

final class PostController extends Controller
{
    public function __construct(
        private readonly PostService $postService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $posts = $this->postService->getPaginated(
            filters: [
                'category_id' => $request->input('category_id'),
                'user_id' => $request->input('user_id'),
                'search' => $request->input('search'),
                'status' => $request->input('status'),
            ],
            perPage: min((int) $request->input('per_page', 15), 100)
        );

        return response()->json([
            'success' => true,
            'data' => $posts->items(),
            'meta' => [
                'current_page' => $posts->currentPage(),
                'last_page' => $posts->lastPage(),
                'per_page' => $posts->perPage(),
                'total' => $posts->total(),
            ],
        ]);
    }

    public function store(StorePostRequest $request): JsonResponse
    {
        $dto = PostDTO::fromRequest($request);
        
        // Set user_id from authenticated user if not provided
        $dto = PostDTO::fromArray(array_merge($dto->toArray(), [
            'user_id' => Auth::id(),
        ]));

        $post = $this->postService->create($dto);

        return response()->json([
            'success' => true,
            'message' => 'Post created successfully',
            'data' => $post,
        ], 201);
    }

    public function show(Post $post): JsonResponse
    {
        $post->load(['category', 'user']);
        
        return response()->json([
            'success' => true,
            'data' => $post,
        ]);
    }

    public function update(UpdatePostRequest $request, Post $post): JsonResponse
    {
        $post = $this->postService->update($post, PostDTO::fromRequest($request));
        
        return response()->json([
            'success' => true,
            'message' => 'Post updated successfully',
            'data' => $post,
        ]);
    }

    public function destroy(Post $post): JsonResponse
    {
        $this->postService->delete($post);
        
        return response()->json([
            'success' => true,
            'message' => 'Post deleted successfully',
        ]);
    }

    public function publish(Post $post): JsonResponse
    {
        $post = $this->postService->publish($post);
        
        return response()->json([
            'success' => true,
            'message' => 'Post published successfully',
            'data' => $post,
        ]);
    }
}
