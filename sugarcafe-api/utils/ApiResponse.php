<?php
class ApiResponse {
    public static function send($status, $message, $data = null) {
        $response = [
            'status' => $status,
            'message' => $message
        ];

        if ($data !== null) {
            $response['data'] = $data;
        }

        header("Content-Type: application/json");
        http_response_code($status);
        echo json_encode($response);
        exit();
    }

    public static function error($status, $message) {
        self::send($status, $message);
    }

    public static function success($message, $data = null) {
        self::send(200, $message, $data);
    }

    public static function created($message, $data = null) {
        self::send(201, $message, $data);
    }

    public static function badRequest($message = 'Bad Request') {
        self::error(400, $message);
    }

    public static function unauthorized($message = 'Unauthorized') {
        self::error(401, $message);
    }

    public static function forbidden($message = 'Forbidden') {
        self::error(403, $message);
    }

    public static function notFound($message = 'Not Found') {
        self::error(404, $message);
    }

    public static function methodNotAllowed($message = 'Method Not Allowed') {
        self::error(405, $message);
    }

    public static function internalServerError($message = 'Internal Server Error') {
        self::error(500, $message);
    }
}

