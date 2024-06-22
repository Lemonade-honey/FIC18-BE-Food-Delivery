<?php

namespace App\Services\Interface;

interface FileService
{
    function saveFileToStoragePath(\Illuminate\Http\UploadedFile $file, string $path): string;
}