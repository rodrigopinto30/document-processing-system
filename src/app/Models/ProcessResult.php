<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProcessResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'process_id',
        'total_words',
        'total_lines',
        'total_characters',
        'most_frequent_words',
        'files_processed'
    ];

    protected $casts = [
        'most_frequent_words' => 'array',
        'files_processed' => 'array'
    ];

    public function process()
    {
        return $this->belongsTo(Process::class);
    }
}
