<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use InvalidArgumentException;

class FileScannerService
{
    protected string $disk = 'local';
    protected string $inputPath = 'documents/input';

    public function listFiles(): array
    {
        if (!Storage::disk($this->disk)->exists($this->inputPath)) {
            return [];
        }

        $files = collect(Storage::disk($this->disk)->files($this->inputPath))
            ->filter(fn($f) => Str::endsWith($f, '.txt'))
            ->values()
            ->all();

        return $files;
    }

    public function validateFiles(array $files): bool
    {
        foreach ($files as $file) {
            if (!Storage::disk($this->disk)->exists($file)) {
                throw new InvalidArgumentException("File not found: $file");
            }
        }
        return true;
    }

    public function readFile(string $file): string
    {
        return Storage::disk($this->disk)->get($file);
    }
}
