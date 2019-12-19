<?php

namespace App\Model;

class City
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $lat;

    /**
     * @var string
     */
    private $lng;

    /**
     * @var string
     */
    private $country;

    /**
     * @param string $name
     * @param string $lat
     * @param string $lng
     * @param string $country
     */
    public function __construct(string $name, string $lat, string $lng, string $country)
    {
        $this->name = $name;
        $this->lat = $lat;
        $this->lng = $lng;
        $this->country = $country;
    }

    /**
     * @param array $data
     *
     * @return static
     */
    public static function createFromArray(array $data): self
    {
        return new self(
            $data['name'],
            $data['lat'],
            $data['lng'],
            $data['country']
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
     * @return string
     */
    public function getLat(): string
    {
        return $this->lat;
    }

    /**
     * @return string
     */
    public function getLng(): string
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
