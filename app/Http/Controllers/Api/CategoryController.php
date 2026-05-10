<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\DataTransferObjects\CategoryDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use App\Services\CategoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class CategoryController extends Controller
{
    public function __construct(
        private readonly CategoryService $categoryService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $categories = $request->boolean('hierarchy')
            ? $this->categoryService->getWithHierarchy()
            : $this->categoryService->getPaginated(
                min((int) $request->input('per_page', 15), 100)
            );

        return $this->successResponse(
            $request->boolean('hierarchy')
                ? CategoryResource::collection($categories)
                : CategoryResource::collection($categories->toResponse()->getData()),
            'Categories retrieved successfully'
        );
    }

    public function store(StoreCategoryRequest $request): JsonResponse
    {
        $category = $this->categoryService->create(
            CategoryDTO::fromRequest($request)
        );

        return $this->successResponse(
            new CategoryResource($category),
            'Category created successfully',
            Response::HTTP_CREATED
        );
    }

    public function show(Category $category): JsonResponse
    {
        return $this->successResponse(
            new CategoryResource($category->load(['parent', 'children'])),
            'Category retrieved successfully'
        );
    }

    public function update(StoreCategoryRequest $request, Category $category): JsonResponse
    {
        $category = $this->categoryService->update($category, CategoryDTO::fromRequest($request));

        return $this->successResponse(
            new CategoryResource($category),
            'Category updated successfully'
        );
    }

    public function destroy(Category $category): JsonResponse
    {
        if ($category->posts()->count() > 0) {
            return $this->errorResponse(
                'Cannot delete category with associated posts',
                Response::HTTP_CONFLICT
            );
        }

        $this->categoryService->delete($category);

        return $this->successResponse(null, 'Category deleted successfully');
    }

    public function toggle(Category $category): JsonResponse
    {
        $category = $this->categoryService->toggleActive($category);

        return $this->successResponse(
            new CategoryResource($category),
            'Category status toggled successfully'
        );
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

    protected function errorResponse(
        string $message = 'Error',
        int $status = Response::HTTP_BAD_REQUEST
    ): JsonResponse {
        return response()->json([
            'success' => false,
            'message' => $message,
        ], $status);
    }
}
