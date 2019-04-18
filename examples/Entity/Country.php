<?php declare(strict_types=1);

namespace HarmonyIO\Orm\Examples\Entity;

use Amp\Promise;
use HarmonyIO\Orm\Entity\Entity;

class Country extends Entity
{
    /** @var Promise<int> */
    private $id;

    /** @var Promise<string> */
    private $alpha2Code;

    /** @var Promise<string> */
    private $alpha3Code;

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
    public function getAlpha2Code(): Promise
    {
        return $this->alpha2Code;
    }

    /**
     * @return Promise<string>
     */
    public function getAlpha3Code(): Promise
    {
        return $this->alpha3Code;
    }
}
