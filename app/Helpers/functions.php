<?php

use Illuminate\Http\Response;

if (! function_exists('apiResponse')) {
    function apiResponse(mixed $data = [], int $code = 200, array $headers = [], int $options = 0): \Illuminate\Http\JsonResponse|Response
    {
        if (is_string($data) || is_numeric($data)) {
            return response($data, $code, $headers);
        }

        return response()->json($data, $code, $headers, $options);
    }
}
