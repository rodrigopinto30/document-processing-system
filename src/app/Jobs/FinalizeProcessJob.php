<?php

namespace App\Jobs;

use App\Enums\ProcessStatusEnum;
use App\Models\DocumentFile;
use App\Models\Process;
use App\Models\ProcessResult;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;

class FinalizeProcessJob implements ShouldQueue
{
    use Queueable, Dispatchable;

    public string $processId;
    /**
     * Create a new job instance.
     */
    public function __construct(string $processId)
    {
        $this->processId = $processId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $process = Process::findOrFail($this->processId);

        $files = DocumentFile::where('process_id', $this->processId)->get();

        $totalWords = $files->sum('word_count');
        $totalLines = $files->sum('lines_count');
        $totalChars = $files->sum('character_count');

        $wordCounts = [];
        foreach ($files as $f) {
            $frequent = $f->frequent_words ?? [];
            foreach ($frequent as $w) {
                $wordCounts[$w] = ($wordCounts[$w] ?? 0) + 1;
            }
        }

        arsort($wordCounts);
        $globalTop = array_slice(array_keys($wordCounts), 0, 10);

        DB::transaction(function () use ($process, $totalWords, $totalLines, $totalChars, $globalTop, $files) {
            ProcessResult::create([
                'process_id' => $process->id,
                'total_words' => $totalWords,
                'total_lines' => $totalLines,
                'total_characters' => $totalChars,
                'most_frequent_words' => $globalTop,
                'files_processed' => $files->pluck('file_name')->toArray()
            ]);

            $process->status = ProcessStatusEnum::COMPLETED->value;
            $process->finished_at = now();
            $process->progress_percentage = 100;
            $process->save();

            $process->logs()->create([
                'level' => 'info',
                'message' => 'Process finalized successfully'
            ]);
        });
    }
}
