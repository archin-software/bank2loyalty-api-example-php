<?php

namespace Example\Examples;

use JsonSerializable;

class Response
{
    /**
     * Respond with JSON.
     *
     * @param JsonSerializable $object
     * @param int $statusCode
     */
    public static function json(JsonSerializable $object, $statusCode = 200): void
    {
        http_response_code($statusCode);
        echo json_encode($object);
        exit;
    }
}
