<?php

namespace App\Services;
use App\Services\Interfaces\FileService;
use Illuminate\Support\Facades\Storage;

class FileServiceImpl implements FileService
{
    /**
     * Generate UUID File
     * 
     * menulis ulang nama file dengaan UUID, lengkap dengan extensionnya
     */
    private function generateUUIDFileName(\Illuminate\Http\UploadedFile $file): string
    {
        return \Illuminate\Support\Str::uuid() . $file->getClientOriginalExtension();
    }

    public function saveFileToStoragePath(\Illuminate\Http\UploadedFile $file, string $path): string
    {
        $fileName = $this->generateUUIDFileName($file);

        $saveFileToPathLocation = Storage::disk('public')->putFileAs($path, $file, $fileName);

        return $saveFileToPathLocation;
    }
}