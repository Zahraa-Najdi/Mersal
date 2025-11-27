<?php

class ResponseService //handles responses for APIs
{

    public static function response(int $status_code, $payload)
    {
        $response = []; //create array
        $response["status"] = $status_code; //add status to the array, ex-> "status": 200
        $response["data"] = $payload; //add data (payload) to the array
        return json_encode($response); //return response in a json array (format)
    }
}

