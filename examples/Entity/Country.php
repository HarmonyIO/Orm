<?php declare(strict_types=1);

namespace HarmonyIO\Orm\Examples\Entity;

use Amp\Promise;
use Amp\Success;
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
     * @return Promise<Country>
     */
    public function setAlpha2Code(string $alpha2Code): Promise
    {
        $this->alpha2Code = new Success($alpha2Code);

        $this->markPropertyAsChanged('alpha2Code');

        return new Success($this);
    }

    /**
     * @return Promise<string>
     */
    public function getAlpha3Code(): Promise
    {
        return $this->alpha3Code;
    }

    /**
     * @return Promise<Country>
     */
    public function setAlpha3Code(string $alpha3Code): Promise
    {
        $this->alpha3Code = new Success($alpha3Code);

        $this->markPropertyAsChanged('alpha3Code');

        return new Success($this);
    }
}
