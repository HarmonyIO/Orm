<?php declare(strict_types=1);

namespace HarmonyIO\Orm\Examples\Entity;

use HarmonyIO\Orm\Entity\Entity;

class CompanyLocation extends Entity
{
    /** @var int */
    private $id;

    /** @var string */
    private $address;

    /** @var string */
    private $city;

    /** @var Country */
    private $country;

    protected function relate(): void
    {
        $this->manyToOne('country', Country::class);
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getAddress(): string
    {
        return $this->address;
    }

    public function getCity(): string
    {
        return $this->city;
    }

    public function getCountry(): Country
    {
        return $this->country;
    }
}
