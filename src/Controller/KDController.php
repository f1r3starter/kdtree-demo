<?php

namespace App\Controller;

use App\Model\CitiesCollection;
use App\Model\City;
use KDTree\Exceptions\PointAlreadyExists;
use KDTree\Interfaces\PointInterface;
use KDTree\Search\NearestSearch;
use KDTree\Structure\KDTree;
use KDTree\ValueObject\Point;
use Psr\Http\Message\ResponseInterface;

final class KDController
{
    use PrepareResponseTrait;

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
            static function(City $city) use ($kdTree) {
                try {
                    $point = new Point($city->getLat(), $city->getLng());
                    $point->setName($this->prepareName($city));
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
     * @return ResponseInterface
     */
    public function __invoke(PointInterface $searchingPoint): ResponseInterface
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

        return $this->preparePostResponse($response);
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
