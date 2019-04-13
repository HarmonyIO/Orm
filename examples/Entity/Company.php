<?php declare(strict_types=1);

namespace HarmonyIO\Orm\Examples\Entity;

use HarmonyIO\Orm\Entity\Entity;

class Company extends Entity
{
    /** @var int */
    private $id;

    /** @var string */
    private $name;

    /** @var CompanyLocation */
    private $location;

    protected function relate(): void
    {
        $this->oneToOne('location', CompanyLocation::class, 'id', 'location_id');
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getLocation(): CompanyLocation
    {
        return $this->location;
    }
}
