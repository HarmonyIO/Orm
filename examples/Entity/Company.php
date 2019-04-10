<?php declare(strict_types=1);

namespace HarmonyIO\Orm\Examples\Entity;

use HarmonyIO\Orm\Entity\Entity;

class Company extends Entity
{
    /** @var int */
    private $id;

    /** @var string */
    private $name;

    /** @var Country */
    private $country;

    protected function relate(): void
    {
        $this->hasOne('country', Country::class);
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getCountry(): Country
    {
        return $this->country;
    }
}
