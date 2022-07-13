<?php

if(!function_exists('apiResp')) {
    function apiResp($data, $status = 'success')
    {
        $response = [
            'status' => $status,
            'data' => $data
        ];

        return $response;
    }
}