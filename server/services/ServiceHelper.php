<?php

require_once(__DIR__ . "/../models/Chat.php");

class ServiceHelper
{
    public static function validateRequiredFields(array $data, array $requiredFields)
    {
        foreach ($requiredFields as $field) {
            if (!isset($data[$field]) || empty(trim($data[$field]))) {
                return [
                    'status' => 400,
                    'data' => ['error' => "Missing or empty required field: {$field}"]
                ];
            }
        }

        return true;
    }

    public static function validateIdExists($connection, $id)
    {
        $data = Chat::find($connection, $id, 'id');

        if (!$data) {
            return [
                'status' => 404,
                'data' => ['error' => 'Wrong id']
            ];
        }

        return [
            'status' => 200,
            'data' => 'Valid'
        ];
    }
}
