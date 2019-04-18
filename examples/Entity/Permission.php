<?php declare(strict_types=1);

namespace HarmonyIO\Orm\Examples\Entity;

use Amp\Promise;
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
     * @return Promise<string|null>
     */
    public function getDescription(): Promise
    {
        return $this->description;
    }
}
