<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Enums\FileStatusEnum;
use Illuminate\Bus\Batchable;
use App\Enums\ProcessStatusEnum;
use App\Models\DocumentFile;
use App\Models\Process;
use App\Models\ProcessLog;
use App\Services\DocumentAnalyzerService;
use App\Services\FileScannerService;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;
use Throwable;

class ProcessFileJob implements ShouldQueue
{
    // use Queueable, Dispatchable, Batchable;
    use Dispatchable, Queueable, Batchable;

    public string $processId;
    public string $file;

    /**
     * Create a new job instance.
     */
    public function __construct(string $processId, string $file)
    {
        $this->processId = $processId;
        $this->file = $file;
    }

    /**
     * Execute the job.
     */
    public function handle(FileScannerService $scanner, DocumentAnalyzerService $analyzer): void
    {
        try {
            $process = Process::find($this->processId);

            if (!$process || in_array($process->status, [ProcessStatusEnum::STOPPED->value, ProcessStatusEnum::FAILED->value])) {
                return;
            }

            if ($process->status === ProcessStatusEnum::PAUSED->value) {
                $this->release(10);
                return;
            }


            $fileContent = $scanner->readFile($this->file);

            if (!$fileContent) {
                throw new \Exception("File {$this->file} could not be read or is empty.");
            }

            $lines = $analyzer->countLines($fileContent);
            $words = $analyzer->countWords($fileContent);
            $chars = $analyzer->countCharacters($fileContent);
            $topWords = $analyzer->extractMostFrequentWords($fileContent, 10);
            $summary = $analyzer->generateSummary($fileContent, 3);

            DB::transaction(function () use ($process, $lines, $words, $chars, $topWords, $summary) {
                $df = DocumentFile::where('process_id', $this->processId)
                    ->where('file_name', $this->file)
                    ->first();

                if ($df) {
                    $df->update([
                        'status' => FileStatusEnum::COMPLETED->value,
                        'word_count' => $words,
                        'line_count' => $lines,
                        'character_count' => $chars,
                        'frequent_words' => $topWords
                    ]);
                } else {
                    DocumentFile::create([
                        'process_id' => $this->processId,
                        'file_name' => $this->file,
                        'status' => FileStatusEnum::COMPLETED->value,
                        'word_count' => $words,
                        'line_count' => $lines,
                        'character_count' => $chars,
                        'frequent_words' => $topWords
                    ]);
                }

                $process->increment('processed_files');
                $percentage = (int)round(($process->processed_files / max(1, $process->total_files)) * 100);
                $process->progress_percentage = $percentage;
                $process->save();

                $process->logs()->create([
                    'level' => 'info',
                    'message' => "Processed file: $this->file"
                ]);
            });
        } catch (\Throwable $e) {
            dump("ERROR EN ProcessFileJob: " . $e->getMessage());
            throw $e;
        }
    }

    public function failed(Throwable $exception)
    {
        $df = DocumentFile::where('process_id', $this->processId)
            ->where('file_name', $this->file)
            ->first();

        if ($df) {
            $df->update(['status' => FileStatusEnum::FAILED->value]);
        }

        $process = Process::find($this->processId);

        if ($process) {
            $process->logs()->create([
                'level' => 'error',
                'message' => "Processing file $this->file failed: " . $exception->getMessage()

            ]);
        }
    }
}
