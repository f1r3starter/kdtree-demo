<?php

namespace App\Controller;

use App\Model\CitiesCollection;
use App\Model\City;
use KDTree\Interfaces\PointInterface;
use KDTree\ValueObject\Point;
use Psr\Http\Message\ResponseInterface;

final class NaiveController
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
        iterator_apply(
            $citiesCollection,
            static function (City $city) {
                $point = new Point($city->getLat(), $city->getLng());
                $point->setName($this->prepareName($city));
                $this->points[] = $point;
            }
        );
    }

    /**
     * @param PointInterface $searchingPoint
     *
     * @return ResponseInterface
     */
    public function __invoke(PointInterface $searchingPoint): ResponseInterface
    {
        $foundPoint = new Point(0, 0);

        array_reduce(
            $this->points,
            static function (float $minDistance, PointInterface $point) use (&$foundPoint, $searchingPoint) {
                $distance = $point->distance($searchingPoint);
                if ($distance < $minDistance) {
                    $foundPoint = $point;

                    return $distance;
                }

                return $minDistance;
            },
            PHP_INT_MAX
        );

        return $this->preparePostResponse(
            [
                'lat' => $foundPoint->getDAxis(0),
                'lng' => $foundPoint->getDAxis(1),
                'name' => $foundPoint->getName()
            ]
        );
    }
}
