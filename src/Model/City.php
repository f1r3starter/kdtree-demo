<?php

namespace App\Model;

final class City
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var float
     */
    private $lat;

    /**
     * @var float
     */
    private $lng;

    /**
     * @var string
     */
    private $country;

    /**
     * @param string $name
     * @param float $lat
     * @param float $lng
     * @param string $country
     */
    private function __construct(string $name, float $lat, float $lng, string $country)
    {
        $this->name = $name;
        $this->lat = $lat;
        $this->lng = $lng;
        $this->country = $country;
    }

    /**
     * @param array $data
     *
     * @return self
     */
    public static function createFromArray(array $data): self
    {
        return new self(
            (string)$data['name'],
            (float)$data['lat'],
            (float)$data['lng'],
            (string)$data['country']
        );
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return float
     */
    public function getLat(): float
    {
        return $this->lat;
    }

    /**
     * @return float
     */
    public function getLng(): float
    {
        return $this->lng;
    }

    /**
     * @return string
     */
    public function getCountry(): string
    {
        return $this->country;
    }
}
