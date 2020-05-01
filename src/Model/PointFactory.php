<?php

namespace App\Model;

use KDTree\Interfaces\PointInterface;
use KDTree\ValueObject\Point;
use Psr\Http\Message\ServerRequestInterface;

final class PointFactory
{
    /**
     * @param ServerRequestInterface $request
     *
     * @return PointInterface
     * @throws \JsonException
     */
    public function createFromRequest(ServerRequestInterface $request): PointInterface
    {
        $body = json_decode($request->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

        return new Point($body['lat'], $body['lng']);
    }
}
