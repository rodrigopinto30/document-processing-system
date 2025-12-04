<?php

namespace App\Jobs;

use App\Enums\ProcessStatusEnum;
use App\Models\DocumentFile;
use App\Models\Process;
use App\Services\FileScannerService;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Bus;
use Throwable;

class StartProcessJob implements ShouldQueue
{
    use Queueable, Dispatchable, Batchable;

    public string $processId;

    public function __construct(string $processId)
    {
        $this->processId = $processId;
    }

    public function handle(FileScannerService $scanner): void
    {
        try {

            $process = Process::findOrFail($this->processId);

            $process->status = ProcessStatusEnum::RUNNING->value;
            $process->started_at = now();
            $process->save();

            $files = $scanner->listFiles();

            foreach ($files as $file) {
                DocumentFile::create([
                    'process_id' => $process->id,
                    'file_name' => $file,
                    'status' => 'PENDING'
                ]);
            }

            $jobs = [];
            foreach ($files as $file) {
                $jobs[] = new ProcessFileJob($this->processId, $file);
            }

            Bus::batch($jobs)
                ->then(function ($batch) use ($process) {
                    FinalizeProcessJob::dispatch($process->id);
                })
                ->catch(function ($batch, Throwable $e) use ($process) {
                    $process->status = ProcessStatusEnum::FAILED->value;
                    $process->save();
                    $process->logs()->create([
                        'level' => 'error',
                        'message' => 'Batch failed: ' . $e->getMessage()
                    ]);
                })
                ->dispatch();
        } catch (\Throwable $e) {
            dump("ERROR EN StartProcessJob: " . $e->getMessage());
            dump($e->getTraceAsString());
            throw $e;
        }
    }
}
