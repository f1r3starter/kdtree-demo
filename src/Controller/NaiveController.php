<?php

namespace App\Controller;

use App\Model\CitiesCollection;
use App\Model\City;
use KDTree\Interfaces\PointInterface;
use KDTree\ValueObject\Point;

final class NaiveController
{
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
            function (City $city) {
                $point = new Point($city->getLat(), $city->getLng());
                $point->setName(
                    sprintf('%s (%s)', $city->getName(), $city->getCountry())
                );
                $this->points[] = $point;
            }
        );
    }

    /**
     * @param PointInterface $searchingPoint
     *
     * @return array
     */
    public function __invoke(PointInterface $searchingPoint): array
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

        return [
            'lat' => $foundPoint->getDAxis(0),
            'lng' => $foundPoint->getDAxis(1),
            'name' => $foundPoint->getName()
        ];
    }
}
