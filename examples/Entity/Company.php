<?php declare(strict_types=1);

namespace HarmonyIO\Orm\Examples\Entity;

use Amp\Promise;
use HarmonyIO\Orm\Entity\Entity;

class Company extends Entity
{
    /** @var Promise<int> */
    private $id;

    /** @var Promise<string> */
    private $name;

    /** @var Promise<CompanyLocation> */
    private $location;

    protected function relate(): void
    {
        $this->oneToOne('location', CompanyLocation::class, 'id', 'location_id');
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
    public function getName(): Promise
    {
        return $this->name;
    }

    /**
     * @return Promise<CompanyLocation>
     */
    public function getLocation(): Promise
    {
        return $this->location;
    }
}
