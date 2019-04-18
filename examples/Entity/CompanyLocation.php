<?php declare(strict_types=1);

namespace HarmonyIO\Orm\Examples\Entity;

use Amp\Promise;
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
     * @return Promise<string>
     */
    public function getCity(): Promise
    {
        return $this->city;
    }

    /**
     * @return Promise<Country>
     */
    public function getCountry(): Promise
    {
        return $this->country;
    }
}
