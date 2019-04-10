<?php declare(strict_types=1);

namespace HarmonyIO\Orm\Examples\Entity;

use HarmonyIO\Orm\Entity\Entity;

class Country extends Entity
{
    /** @var int */
    private $id;

    /** @var string */
    private $alpha2Code;

    /** @var string */
    private $alpha3Code;

    public function getId(): int
    {
        return $this->id;
    }

    public function getAlpha2Code(): string
    {
        return $this->alpha2Code;
    }

    public function getAlpha3Code(): string
    {
        return $this->alpha3Code;
    }
}
