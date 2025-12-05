<?php

namespace App\Models;

use App\Enums\FileStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentFile extends Model
{
    use HasFactory;

    protected $fillable = [
        'process_id',
        'file_name',
        'status',
        'word_count',
        'line_count',
        'character_count',
        'frequent_words'
    ];

    protected $casts = [
        'status' => FileStatusEnum::class,
        'frequent_words' => 'array'
    ];

    public function process()
    {
        return $this->belongsTo(Process::class);
    }
}
