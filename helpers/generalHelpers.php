<?php

use Illuminate\Http\JsonResponse;

if (! function_exists('sendResponse')) {
    function sendResponse($data, string $message, int $code = 200): JsonResponse {
        if ($code >= 400) {
            return response()->json([
                'status' => 'error',
                'message' => $message,
                'errors' => $data,
            ], $code);
        }

        return response()->json([
            'status' => 'success',
            'message' => $message,
            'data' => $data,
        ], $code);
    }
}


