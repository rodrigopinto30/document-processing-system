<?php

namespace App\Models;

use App\Enums\ProcessStatusEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUlids;

class Process extends Model
{
    use HasFactory, HasUlids;

    protected $fillable = [
        'status',
        'total_files',
        'processed_files',
        'progress_percentage',
        'started_at',
        'finished_at'
    ];

    protected $casts = [
        'status' => ProcessStatusEnum::class,
        'started_at' => 'datetime',
        'finished_at' => 'datetime'
    ];

    public function results()
    {
        return $this->hasOne(ProcessResult::class);
    }

    public function logs()
    {
        return $this->hasMany(ProcessLog::class);
    }

    public function files()
    {
        return $this->hasMany(DocumentFile::class);
    }
}
