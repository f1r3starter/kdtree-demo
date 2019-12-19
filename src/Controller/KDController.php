<?php

namespace App\Controller;

use App\Model\CitiesCollection;
use KDTree\Exceptions\PointAlreadyExists;
use KDTree\KDTree;
use KDTree\Search\NearestSearch;
use KDTree\ValueObject\Point;
use Psr\Http\Message\{ResponseInterface, ServerRequestInterface};

class KDController
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

        /**
         * @var City $city
         */
        foreach ($citiesCollection as $city) {
            try {
                $point = new Point($city->getLat(), $city->getLng());
                $point->setName($city->getName());
                $kdTree->put($point);
            } catch (PointAlreadyExists $e) {
            }
        }

        $this->search = new NearestSearch($kdTree);
    }

    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        $body = json_decode($request->getBody()->getContents(), true);
        $point = $this->search->nearest(new Point($body['lat'], $body['lng']));

        return $this->preparePostResponse(
            [
                'lat' => $point->getDAxis(0),
                'lng' => $point->getDAxis(1),
                'name' => $point->getName()
            ]
        );
    }
}
