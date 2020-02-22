<?php

namespace App\Controller;

use Psr\Http\Message\ResponseInterface;
use React\Http\Response;

class ResponseFormatter
{
    /**
     * @param array $response
     *
     * @return ResponseInterface
     */
    public function prepareResponse(array $response): ResponseInterface
    {
        return new Response(
            200,
            [
                'Content-Type' => 'application/json',
                'Access-Control-Allow-Origin' => '*',
                'Access-Control-Allow-Credentials' => 'true',
                'Access-Control-Allow-Methods' => 'GET,HEAD,OPTIONS',
                'Access-Control-Allow-Headers' => 'Access-Control-Allow-Headers, Origin,Accept, X-Requested-With, Content-Type, Access-Control-Request-Method, Access-Control-Request-Headers',
            ],
            json_encode($response, JSON_THROW_ON_ERROR, 512)
        );
    }
}
