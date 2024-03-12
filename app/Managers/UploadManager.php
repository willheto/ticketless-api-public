<?php

namespace App\Managers;

use App\Models\File;
use Illuminate\Support\Facades\Log;

class UploadManager
{

    public function handleUploadFile(string $base64File, string $fileName): string
    {
        $fileType = mime_content_type($base64File);
        if (!$fileType) {
            throw new \Exception('Invalid base64 file');
        }

        $this->checkIfFileTypeIsValid($fileType);

        $fileData = $this->decodeBase64File($base64File);
        $this->saveFile($fileData, $fileName);

        $fileUrl = url('uploads/' . $fileName);
        return $fileUrl;
    }

    public function deleteFile(string $fileName): void
    {
        // @phpstan-ignore-next-line
        $filePath = app()->basePath('public') . '/uploads/' . $fileName;
        if (file_exists($filePath)) {
            unlink($filePath);
        }
    }

    protected function saveFile(string $fileData, string $fileName): void
    {
        # @phpstan-ignore-next-line
        $filePath = app()->basePath('public') . '/uploads/' . $fileName;
        file_put_contents($filePath, $fileData);
    }

    protected function decodeBase64File(string $base64File): string
    {
        $base64data = explode(',', $base64File, 2)[1];
        $fileData = base64_decode($base64data);

        return $fileData;
    }

    protected function checkIfFileTypeIsValid(string $fileType): void
    {
        $allowedMimeTypes = [
            'image/jpeg',
            'image/gif',
            'image/png',
            'image/bmp',
            'image/svg+xml',
            'image/webp',
            'image/tiff',
        ];

        if (!in_array($fileType, $allowedMimeTypes)) {
            throw new \Exception('Invalid file type: ' . $fileType);
        }
    }
}
