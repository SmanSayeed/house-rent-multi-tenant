<?php

namespace App\Helpers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class ResponseHelper
{
    /**
     * Success response
     */
    public static function success(
        $data = null,
        string $message = 'Success',
        int $code = Response::HTTP_OK,
        array $meta = []
    ): JsonResponse {
        $response = [
            'success' => true,
            'message' => $message,
            'data' => $data,
        ];

        if (!empty($meta)) {
            $response['meta'] = $meta;
        }

        return response()->json($response, $code);
    }

    /**
     * Error response
     */
    public static function error(
        string $message = 'Error',
        int $code = Response::HTTP_BAD_REQUEST,
        $errors = null,
        array $meta = []
    ): JsonResponse {
        $response = [
            'success' => false,
            'message' => $message,
        ];

        if ($errors !== null) {
            $response['errors'] = $errors;
        }

        if (!empty($meta)) {
            $response['meta'] = $meta;
        }

        return response()->json($response, $code);
    }

    /**
     * Validation error response
     */
    public static function validationError(
        $errors,
        string $message = 'Validation failed',
        int $code = Response::HTTP_UNPROCESSABLE_ENTITY
    ): JsonResponse {
        return self::error($message, $code, $errors);
    }

    /**
     * Unauthorized response
     */
    public static function unauthorized(
        string $message = 'Unauthorized'
    ): JsonResponse {
        return self::error($message, Response::HTTP_UNAUTHORIZED);
    }

    /**
     * Forbidden response
     */
    public static function forbidden(
        string $message = 'Forbidden'
    ): JsonResponse {
        return self::error($message, Response::HTTP_FORBIDDEN);
    }

    /**
     * Not found response
     */
    public static function notFound(
        string $message = 'Resource not found'
    ): JsonResponse {
        return self::error($message, Response::HTTP_NOT_FOUND);
    }

    /**
     * Server error response
     */
    public static function serverError(
        string $message = 'Internal server error'
    ): JsonResponse {
        return self::error($message, Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    /**
     * Created response
     */
    public static function created(
        $data = null,
        string $message = 'Resource created successfully'
    ): JsonResponse {
        return self::success($data, $message, Response::HTTP_CREATED);
    }

    /**
     * Updated response
     */
    public static function updated(
        $data = null,
        string $message = 'Resource updated successfully'
    ): JsonResponse {
        return self::success($data, $message, Response::HTTP_OK);
    }

    /**
     * Deleted response
     */
    public static function deleted(
        string $message = 'Resource deleted successfully'
    ): JsonResponse {
        return self::success(null, $message, Response::HTTP_OK);
    }

    /**
     * Paginated response
     */
    public static function paginated(
        $data,
        string $message = 'Data retrieved successfully'
    ): JsonResponse {
        return self::success($data, $message, Response::HTTP_OK);
    }

    /**
     * No content response
     */
    public static function noContent(): JsonResponse
    {
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Custom response with specific structure
     */
    public static function custom(
        array $data,
        int $code = Response::HTTP_OK
    ): JsonResponse {
        return response()->json($data, $code);
    }

    /**
     * Response with additional headers
     */
    public static function withHeaders(
        $data = null,
        string $message = 'Success',
        int $code = Response::HTTP_OK,
        array $headers = []
    ): JsonResponse {
        $response = self::success($data, $message, $code);

        foreach ($headers as $key => $value) {
            $response->header($key, $value);
        }

        return $response;
    }

    /**
     * API token response
     */
    public static function withToken(
        $user,
        string $token,
        string $message = 'Login successful'
    ): JsonResponse {
        return self::success([
            'user' => $user,
            'token' => $token,
            'token_type' => 'Bearer'
        ], $message, Response::HTTP_OK);
    }

    /**
     * Logout response
     */
    public static function loggedOut(
        string $message = 'Logged out successfully'
    ): JsonResponse {
        return self::success(null, $message, Response::HTTP_OK);
    }
}
