<?php declare(strict_types=1);

namespace HarmonyIO\Orm\Examples\Entity;

use Amp\Promise;
use Amp\Success;
use HarmonyIO\Orm\Entity\Entity;

class Permission extends Entity
{
    /** @var Promise<int> */
    private $id;

    /** @var Promise<string> */
    private $name;

    /** @var Promise<string|null> */
    private $description;

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
     * @return Promise<Permission>
     */
    public function setName(string $name): Promise
    {
        $this->name = new Success($name);

        $this->markPropertyAsChanged('name');

        return new Success($this);
    }

    /**
     * @return Promise<string|null>
     */
    public function getDescription(): Promise
    {
        return $this->description;
    }

    /**
     * @return Promise<Permission>
     */
    public function setDescription(string $description): Promise
    {
        $this->description = new Success($description);

        $this->markPropertyAsChanged('description');

        return new Success($this);
    }
}
