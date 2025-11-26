<?php

namespace App\Http\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

trait ApiResponseTrait
{
    protected function successResponse($data, string $message = 'Request successful.', int $code = 200): JsonResponse
    {
        return response()->json([
            'status'  => 'success',
            'message' => $message,
            'data'    => $data,
        ], $code);
    }

    protected function errorResponse(string $message, int $code = 400, ?\Exception $e = null): JsonResponse
    {
        if ($e) {
            Log::error($e->getMessage() . ' in ' . $e->getFile() . ' on line ' . $e->getLine());
        }

        return response()->json([
            'status'  => 'error',
            'message' => $message,
            'data'    => null,
        ], $code);
    }

    protected function emptyResponse(string $message = 'No records found.'): JsonResponse
    {
        return response()->json([
            'status'  => 'empty',
            'message' => $message,
            'data'    => null,
        ], 200);
    }
}