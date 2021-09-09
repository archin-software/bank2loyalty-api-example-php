<?php

namespace Example\Examples;

use JsonSerializable;

/**
 * Just a basic implementation to response with JSON and a status code.
 */
class Response
{
    /**
     * Respond with JSON.
     *
     * @param JsonSerializable $object
     * @param int $statusCode
     */
    public static function json(JsonSerializable $object, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        echo json_encode($object);
        exit;
    }
}
