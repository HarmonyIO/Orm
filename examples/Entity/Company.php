<?php declare(strict_types=1);

namespace HarmonyIO\Orm\Examples\Entity;

use Amp\Promise;
use Amp\Success;
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
     * @return Promise<Company>
     */
    public function setName(string $name): Promise
    {
        $this->name = new Success($name);

        return new Success($this);
    }

    /**
     * @return Promise<CompanyLocation>
     */
    public function getLocation(): Promise
    {
        return $this->location;
    }

    /**
     * @return Promise<Company>
     */
    public function setLocation(CompanyLocation $location): Promise
    {
        $this->location = new Success($location);

        return new Success($this);
    }
}
