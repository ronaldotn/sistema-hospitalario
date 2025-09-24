<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;

class BaseController extends Controller
{
    /**
     * Enviar respuesta de éxito.
     *
     * @param mixed  $data      Datos de la respuesta
     * @param string $message   Mensaje a mostrar
     * @param int    $code      Código de respuesta HTTP (por defecto 200)
     * @return JsonResponse
     */
    public function sendResponse($data, $message, $code = 200, $status = 'success'): JsonResponse
    {
        return response()->json([
            'status'  => $status,
            'code'    => $code,
            'message' => $message,
            'result' => $data,
        ], $code);
    }

    /**
     * Enviar respuesta de error.
     *
     * @param string $message        Mensaje de error
     * @param array  $errorMessages  Errores adicionales (opcional)
     * @param int    $code           Código de error (por defecto 400)
     * @param int    $status         Estado de Codigo (por defecto warning)
     * @return JsonResponse
     */
    public function sendError($message, $errorMessages = [], $code = 400, $status = 'warning'): JsonResponse
    {
        return response()->json([
            'status'  => $status,
            'code'    => $code,
            'message' => $message,
            'result' => is_array($errorMessages) ? implode(' ', $errorMessages) : $message,
        ], $code);
    }
}
