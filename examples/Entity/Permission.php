<?php declare(strict_types=1);

namespace HarmonyIO\Orm\Examples\Entity;

use HarmonyIO\Orm\Entity\Entity;

class Permission extends Entity
{
    /** @var int */
    private $id;

    /** @var string */
    private $name;

    /** @var string|null */
    private $description;

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }
}
