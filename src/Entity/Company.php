<?php declare(strict_types=1);

namespace HarmonyIO\Orm\Entity;

class Company
{
    private const TABLE = 'companies';

    /** @var int */
    private $id;

    /** @var string */
    private $name;

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
