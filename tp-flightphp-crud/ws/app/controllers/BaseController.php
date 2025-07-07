<?php

class BaseController {
    protected function jsonResponse($data, $status = 200) {
        Flight::response()->status($status);
        Flight::response()->header('Content-Type', 'application/json');
        Flight::json($data);
    }

    protected function errorResponse($message, $status = 400) {
        $this->jsonResponse(['error' => $message], $status);
    }

    protected function successResponse($message, $data = null) {
        $response = ['message' => $message];
        if ($data !== null) {
            $response['data'] = $data;
        }
        $this->jsonResponse($response);
    }

    protected function validateRequired($data, $fields) {
        $missing = [];
        foreach ($fields as $field) {
            if (!isset($data[$field]) || empty($data[$field])) {
                $missing[] = $field;
            }
        }
        return $missing;
    }

    protected function render($view, $data = []) {
        Flight::render($view, $data);
    }
}
