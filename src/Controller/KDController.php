<?php

namespace App\Controller;

use App\Model\CitiesCollection;
use App\Model\City;
use KDTree\Exceptions\PointAlreadyExists;
use KDTree\Interfaces\PointInterface;
use KDTree\Search\NearestSearch;
use KDTree\Structure\KDTree;
use KDTree\ValueObject\Point;

final class KDController
{
    /**
     * @var NearestSearch
     */
    private $search;

    /**
     * @param CitiesCollection $citiesCollection
     */
    public function __construct(CitiesCollection $citiesCollection)
    {
        $kdTree = new KDTree(2);

        iterator_apply(
            $citiesCollection,
            static function (City $city) use ($kdTree) {
                try {
                    $point = new Point($city->getLat(), $city->getLng());
                    $point->setName(
                        sprintf('%s (%s)', $city->getName(), $city->getCountry())
                    );
                    $kdTree->put($point);
                } catch (PointAlreadyExists $e) {
                }
            }
        );

        $this->search = new NearestSearch($kdTree);
    }

    /**
     * @param PointInterface $searchingPoint
     *
     * @return array
     */
    public function __invoke(PointInterface $searchingPoint): array
    {
        $point = $this->search->nearest($searchingPoint);

        if (null === $point) {
            $response = $this->formatResponse(null, null, null);
        } else {
            $response = $this->formatResponse(
                $point->getDAxis(0),
                $point->getDAxis(1),
                $point->getName()
            );
        }

        return $response;
    }

    /**
     * @param float|null $lat
     * @param float|null $lng
     * @param string|null $name
     *
     * @return array
     */
    private function formatResponse(?float $lat, ?float $lng, ?string $name): array
    {
        return [
            'lat' => $lat,
            'lng' => $lng,
            'name' => $name,
        ];
    }
}
