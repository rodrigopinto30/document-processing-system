<?php

namespace App\Http\Controllers;

use App\Services\ProcessService;
use Illuminate\Http\JsonResponse;

class ProcessController extends Controller
{
    public function __construct(
        private ProcessService $processService
    ) {}

    public function start(): JsonResponse
    {
        try {
            $process = $this->processService->start();

            return response()->json([
                "message" => "Process started successfully",
                "process_id" => $process->id
            ], 201);
        } catch (\Throwable $e) {
            return response()->json([
                "error" => "Failed to start process",
                "details" => $e->getMessage()
            ], 400);
        }
    }

    public function stop(string $process_id): JsonResponse
    {
        try {
            $this->processService->stop($process_id);

            return response()->json([
                "message" => "Process stopped",
                "process_id" => $process_id
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                "error" => "Failed to stop process",
                "details" => $e->getMessage()
            ], 400);
        }
    }

    public function status(string $process_id): JsonResponse
    {
        try {
            $status = $this->processService->status($process_id);

            return response()->json($status);
        } catch (\Throwable $e) {
            return response()->json([
                "error" => "Failed to get process status",
                "details" => $e->getMessage()
            ], 404);
        }
    }

    public function list(): JsonResponse
    {
        return response()->json([
            "message" => "Process list retrieved",
            "data" => $this->processService->list()
        ]);
    }

    public function results(string $process_id): JsonResponse
    {
        try {
            $results = $this->processService->results($process_id);

            return response()->json([
                "process_id" => $process_id,
                "results" => $results
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                "error" => "Failed to get process results",
                "details" => $e->getMessage()
            ], 404);
        }
    }
}
