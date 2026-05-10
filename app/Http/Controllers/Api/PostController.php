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
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
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
                'author_id' => $request->input('author_id'),
                'is_published' => $request->has('is_published') 
                    ? $request->boolean('is_published') 
                    : null,
                'search' => $request->input('search'),
                'sort_by' => $request->input('sort_by', 'created_at'),
                'sort_order' => $request->input('sort_order', 'desc'),
            ],
            perPage: min((int) $request->input('per_page', 15), 100)
        );

        return $this->successResponse(PostResource::collection($posts->toResponse()->getData()), 'Posts retrieved successfully');
    }

    public function store(StorePostRequest $request): JsonResponse
    {
        $post = $this->postService->create(
            PostDTO::fromRequest($request),
            $request->user()->id
        );

        return $this->successResponse(
            new PostResource($post->load(['category', 'author'])),
            'Post created successfully',
            Response::HTTP_CREATED
        );
    }

    public function show(Post $post): JsonResponse
    {
        $post->load(['category', 'author']);
        return $this->successResponse(new PostResource($post), 'Post retrieved successfully');
    }

    public function update(UpdatePostRequest $request, Post $post): JsonResponse
    {
        $post = $this->postService->update($post, PostDTO::fromRequest($request));

        return $this->successResponse(
            new PostResource($post->load(['category', 'author'])),
            'Post updated successfully'
        );
    }

    public function destroy(Post $post): JsonResponse
    {
        $this->postService->delete($post);

        return $this->successResponse(null, 'Post deleted successfully');
    }

    public function publish(Post $post): JsonResponse
    {
        $post = $this->postService->publish($post);

        return $this->successResponse(new PostResource($post), 'Post published successfully');
    }

    public function unpublish(Post $post): JsonResponse
    {
        $post = $this->postService->unpublish($post);

        return $this->successResponse(new PostResource($post), 'Post unpublished successfully');
    }

    protected function successResponse(
        mixed $data = null,
        string $message = 'Success',
        int $status = Response::HTTP_OK
    ): JsonResponse {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $status);
    }
}
