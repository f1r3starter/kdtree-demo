<?php

namespace App\Controller;

use App\Model\CitiesCollection;
use App\Model\City;
use KDTree\ValueObject\Point;
use Psr\Http\Message\ServerRequestInterface;

class NaiveController
{
    use PrepareResponseTrait;

    /**
     * @var Point[]
     */
    private $points;

    /**
     * @param CitiesCollection $citiesCollection
     */
    public function __construct(CitiesCollection $citiesCollection)
    {
        /**
         * @var City $city
         */
        foreach ($citiesCollection as $city) {
            $point = new Point($city->getLat(), $city->getLng());
            $point->setName($city->getName());
            $this->points[] = $point;
        }
    }

    public function __invoke(ServerRequestInterface $request)
    {
        $body = json_decode($request->getBody()->getContents(), true);
        $searchingPoint = new Point($body['lat'], $body['lng']);
        $foundPoint = null;
        $minDistance = PHP_INT_MAX;
        foreach ($this->points as $point) {
            $distance = $point->distance($searchingPoint);
            if ($distance < $minDistance) {
                $minDistance = $distance;
                $foundPoint = $point;
            }
        }

        return $this->preparePostResponse(
            [
                'lat' => $foundPoint->getDAxis(0),
                'lng' => $foundPoint->getDAxis(1),
                'name' => $foundPoint->getName()
            ]
        );
    }
}
