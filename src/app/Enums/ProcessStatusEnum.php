<?php

namespace App\Enums;

enum ProcessStatusEnum: string
{
    case PENDING = 'PENDING';
    case RUNNING = 'RUNNING';
    case PAUSED = 'PAUSED';
    case COMPLETED = 'COMPLETED';
    case FAILED = 'FAILED';
    case STOPPED = 'STOPPED';
}
