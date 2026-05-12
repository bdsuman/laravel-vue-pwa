<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\DataTransferObjects\PostDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Http\Resources\PostResource;
use App\Models\Post;
use App\Services\PostService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

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
            'data' => PostResource::collection($posts),
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
        $post = $this->postService->create(PostDTO::fromRequest($request));

        return response()->json([
            'success' => true,
            'message' => 'Post created successfully',
            'data' => new PostResource($post),
        ], Response::HTTP_CREATED);
    }

    public function show(Post $post): JsonResponse
    {
        $post->load(['category', 'user']);
        
        return response()->json([
            'success' => true,
            'data' => new PostResource($post),
        ]);
    }

    public function update(UpdatePostRequest $request, Post $post): JsonResponse
    {
        $post = $this->postService->update($post, PostDTO::fromRequest($request));
        
        return response()->json([
            'success' => true,
            'message' => 'Post updated successfully',
            'data' => new PostResource($post),
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
            'data' => new PostResource($post),
        ]);
    }

    public function unpublish(Post $post): JsonResponse
    {
        $post = $this->postService->unpublish($post);
        
        return response()->json([
            'success' => true,
            'message' => 'Post unpublished successfully',
            'data' => new PostResource($post),
        ]);
    }
}
