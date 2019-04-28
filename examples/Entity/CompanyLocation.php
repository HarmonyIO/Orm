<?php declare(strict_types=1);

namespace HarmonyIO\Orm\Examples\Entity;

use Amp\Promise;
use Amp\Success;
use HarmonyIO\Orm\Entity\Entity;

class CompanyLocation extends Entity
{
    /** @var Promise<int> */
    private $id;

    /** @var Promise<string> */
    private $address;

    /** @var Promise<string> */
    private $city;

    /** @var Promise<Country> */
    private $country;

    protected function relate(): void
    {
        $this->manyToOne('country', Country::class);
    }

    /**
     * @return Promise<int>
     */
    public function getId(): Promise
    {
        return $this->id;
    }

    /**
     * @return Promise<string>
     */
    public function getAddress(): Promise
    {
        return $this->address;
    }

    /**
     * @return Promise<CompanyLocation>
     */
    public function setAddress(string $address): Promise
    {
        $this->address = new Success($address);

        $this->markPropertyAsChanged('address');

        return new Success($this);
    }

    /**
     * @return Promise<string>
     */
    public function getCity(): Promise
    {
        return $this->city;
    }

    /**
     * @return Promise<CompanyLocation>
     */
    public function setCity(string $city): Promise
    {
        $this->city = new Success($city);

        $this->markPropertyAsChanged('city');

        return new Success($this);
    }

    /**
     * @return Promise<Country>
     */
    public function getCountry(): Promise
    {
        return $this->country;
    }

    /**
     * @return Promise<CompanyLocation>
     */
    public function setCountry(Country $country): Promise
    {
        $this->country = new Success($country);

        $this->markPropertyAsChanged('country');

        return new Success($this);
    }
}
