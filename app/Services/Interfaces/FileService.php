<?php

namespace App\Services\Interfaces;

interface FileService
{
    function saveFileToStoragePath(\Illuminate\Http\UploadedFile $file, string $path): string;
}