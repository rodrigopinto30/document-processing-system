<?php

namespace App\Services;

use App\Enums\ProcessStatusEnum;
use App\Models\Process;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Jobs\StartProcessJob;

class ProcessService
{
    protected FileScannerService $scanner;

    public function __construct(FileScannerService $scanner)
    {
        $this->scanner = $scanner;
    }

    public function start(?string $name = null): Process
    {
        $files = $this->scanner->listFiles();
        if (empty($files)) {
            throw new \RuntimeException('No files found in documents input folder.');
        }

        return DB::transaction(function () use ($name, $files) {
            $process = Process::create([
                'id' => (string) Str::uuid(),
                'status' => ProcessStatusEnum::PENDING->value,
                'total_files' => count($files),
                'processed_files' => 0,
                'progress_percentage' => 0,
                'started_at' => null,
            ]);

            StartProcessJob::dispatch($process->id);

            return $process;
        });
    }

    public function stop(string $processId): void
    {
        $process = Process::findOrFail($processId);
        $process->status = ProcessStatusEnum::STOPPED->value;
        $process->save();
        $process->logs()->create([
            'level' => 'info',
            'message' => 'Process manually stopped'
        ]);
    }

    public function list(int $limit = 50)
    {
        return Process::orderBy('created_at', 'desc')->paginate($limit);
    }

    public function status(string $processId): array
    {
        $p = Process::with('results')->findOrFail($processId);
        return [
            'process_id' => $p->id,
            'status' => $p->status,
            'progress' => [
                'total_files' => $p->total_files,
                'processed_files' => $p->processed_files,
                'percentage' => $p->progress_percentage
            ],
            'started_at' => $p->started_at?->toIso8601String(),
            'estimated_completion' => null,
            'results' => $p->results?->toArray()
        ];
    }

    public function result(string $processId): array
    {
        $p = Process::with('results')->findOrFail($processId);
        if (!$p->results) {
            return ['message' => 'Results not available yet'];
        }
        return $p->results->toArray();
    }

    public function results(string $processId): array
    {
        $p = Process::with('results')->findOrFail($processId);
        if (!$p->results) {
            return ['message' => 'Results not available yet'];
        }

        return $p->results->toArray();
    }
}
