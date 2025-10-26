<?php

namespace App\Helpers;

use Illuminate\Http\JsonResponse;

class ApiResponse
{
    /**
     * Respuesta exitosa
     */
    public static function success($data = null, string $message = null, int $code = 200, array $meta = []): JsonResponse
    {
        $response = [
            'status' => 'success',
            'message' => $message ?? 'Operación exitosa',
            'data' => $data,
        ];

        if (!empty($meta)) {
            $response['meta'] = $meta;
        }

        return response()->json($response, $code);
    }

    /**
     * Respuesta de error
     */
    public static function error(string $message = 'Ocurrió un error', int $code = 500, $errors = null): JsonResponse
    {
        $response = [
            'status' => 'error',
            'message' => $message,
        ];

        if ($errors) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $code);
    }
}
