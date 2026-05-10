<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\FileUploadService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class FileUploadController extends Controller
{
    public function __construct(
        private readonly FileUploadService $fileUploadService
    ) {}

    /**
     * Upload single file with optional crop params
     * 
     * @param Request $request
     * Crop params: width, height, x, y, quality
     */
    public function upload(Request $request): JsonResponse
    {
        $request->validate([
            'file' => 'required|file|max:10240', // 10MB max
        ]);

        $options = [
            'directory' => $request->input('directory', 'uploads'),
            'disk' => $request->input('disk', 'public'),
            'quality' => $request->input('quality', 85),
        ];

        // Crop parameters (can be JSON string or array)
        if ($request->has('crop')) {
            $cropData = is_string($request->input('crop')) 
                ? json_decode($request->input('crop'), true) 
                : $request->input('crop');
            
            if ($cropData && is_array($cropData)) {
                $options['crop'] = [
                    'width' => (int) ($cropData['width'] ?? 0),
                    'height' => (int) ($cropData['height'] ?? 0),
                    'x' => (int) ($cropData['x'] ?? 0),
                    'y' => (int) ($cropData['y'] ?? 0),
                ];
            }
        }

        // Resize parameters
        if ($request->has('width') || $request->has('height')) {
            $options['width'] = $request->input('width') ? (int) $request->input('width') : null;
            $options['height'] = $request->input('height') ? (int) $request->input('height') : null;
            $options['fit'] = $request->boolean('fit', false);
        }

        $result = $this->fileUploadService->upload($request->file('file'), $options);

        return response()->json([
            'success' => true,
            'message' => __('messages.file_uploaded'),
            'data' => $result,
        ], Response::HTTP_CREATED);
    }

    /**
     * Delete file
     */
    public function delete(Request $request): JsonResponse
    {
        $request->validate([
            'path' => 'required|string',
        ]);

        $this->fileUploadService->delete(
            $request->input('path'),
            $request->input('disk', 'public')
        );

        return response()->json([
            'success' => true,
            'message' => __('messages.file_deleted'),
        ]);
    }
}
