<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

final class FileUploadService
{
    private ImageManager $imageManager;

    public function __construct()
    {
        $this->imageManager = new ImageManager(new Driver());
    }

    /**
     * Upload file with optional crop/resize
     */
    public function upload(UploadedFile $file, array $options = []): array
    {
        $directory = $options['directory'] ?? 'uploads';
        $disk = $options['disk'] ?? 'public';
        
        // Generate unique filename
        $filename = $this->generateFilename($file);
        $path = "{$directory}/{$filename}";
        
        // Process image if it's an image
        if ($this->isImage($file)) {
            $processedPath = $this->processImage($file, $options);
            Storage::disk($disk)->putFileAs($directory, new UploadedFile($processedPath, $filename), $options['visibility'] ?? 'public');
            @unlink($processedPath);
        } else {
            Storage::disk($disk)->putFileAs($directory, $file, $options['visibility'] ?? 'public');
        }

        return [
            'path' => Storage::disk($disk)->url($path),
            'filename' => $filename,
            'original_name' => $file->getClientOriginalName(),
            'size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
        ];
    }

    /**
     * Process image with crop and resize
     */
    public function processImage(UploadedFile $file, array $options): string
    {
        $image = $this->imageManager->read($file->getPathname());
        
        // Crop if coordinates provided
        if (isset($options['crop'])) {
            $image->crop(
                (int) $options['crop']['width'],
                (int) $options['crop']['height'],
                (int) $options['crop']['x'],
                (int) $options['crop']['y']
            );
        }

        // Resize if dimensions provided
        if (isset($options['width']) || isset($options['height'])) {
            $width = $options['width'] ?? null;
            $height = $options['height'] ?? null;
            $fit = $options['fit'] ?? false;
            
            if ($fit) {
                $image->cover($width, $height);
            } else {
                $image->resize($width, $height, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });
            }
        }

        // Quality
        $quality = $options['quality'] ?? 85;
        
        // Save to temp file
        $tempPath = tempnam(sys_get_temp_dir(), 'upload_') . '.' . $file->getClientOriginalExtension();
        $image->save($tempPath, $quality);

        return $tempPath;
    }

    /**
     * Delete file
     */
    public function delete(string $path, string $disk = 'public'): bool
    {
        $relativePath = str_replace(Storage::disk($disk)->url(''), '', $path);
        return Storage::disk($disk)->delete($relativePath);
    }

    /**
     * Generate unique filename
     */
    private function generateFilename(UploadedFile $file): string
    {
        $extension = $file->getClientOriginalExtension();
        return time() . '_' . uniqid() . '.' . $extension;
    }

    /**
     * Check if file is an image
     */
    private function isImage(UploadedFile $file): bool
    {
        return str_starts_with($file->getMimeType(), 'image/');
    }
}
