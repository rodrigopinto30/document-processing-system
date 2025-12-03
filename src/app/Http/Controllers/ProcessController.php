<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProcessController extends Controller
{
    public function start(): JsonResponse
    {
        return response()->json(["message" => "Process started"]);
    }

    public function stop(int $process_id): JsonResponse
    {
        return response()->json(["message" => "Process stoped", "process_id" => $process_id]);
    }

    public function status(int $process_id): JsonResponse
    {
        return response()->json(["message" => "Process status retrieved", "process_id" => $process_id]);
    }

    public function list(): JsonResponse
    {
        return response()->json(["message" => "Process list retrieved"]);
    }

    public function results(int $process_id): JsonResponse
    {
        return response()->json(["message" => "Process results retrieved", "process_id" => $process_id]);
    }
}
