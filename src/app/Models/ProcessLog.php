<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProcessLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'process_id',
        'level',
        'message',
        'logged_at'
    ];

    protected $casts = [
        'logged_at' => 'datetime'
    ];

    public function process()
    {
        return $this->belongsTo(Process::class);
    }
}
