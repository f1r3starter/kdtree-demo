<?php

namespace App\Controller;

use App\Model\CitiesCollection;
use App\Model\City;
use KDTree\Exceptions\PointAlreadyExists;
use KDTree\Search\NearestSearch;
use KDTree\Structure\KDTree;
use KDTree\ValueObject\Point;
use Psr\Http\Message\{ResponseInterface, ServerRequestInterface};

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

        /**
         * @var City $city
         */
        foreach ($citiesCollection as $city) {
            try {
                $point = new Point($city->getLat(), $city->getLng());
                $point->setName($this->prepareName($city));
                $kdTree->put($point);
            } catch (PointAlreadyExists $e) {
            }
        }

        $this->search = new NearestSearch($kdTree);
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        $body = json_decode($request->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);
        $point = $this->search->nearest(new Point($body['lat'], $body['lng']));

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
