<?php

namespace App\Controller;

use React\Http\Response;

trait PrepareResponseTrait
{
    /**
     * @param array $response
     *
     * @return Response
     */
    private function preparePostResponse(array $response): Response
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
            json_encode($response)
        );
    }
}
