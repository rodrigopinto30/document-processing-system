<?php

namespace App\Enums;

enum ProcessStatusEnum: string
{
    case PENDING = 'PENDING';
    case RUNNUNG = 'RUNNING';
    case PAUSED = 'PAUSED';
    case COMPLETED = 'COMPLETED';
    case FIALED = 'FAILED';
    case STOPPED = 'STOPPED';
}
