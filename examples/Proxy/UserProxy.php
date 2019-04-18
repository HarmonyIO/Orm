<?php declare(strict_types=1);

namespace HarmonyIO\Orm\Examples\Proxy;

use HarmonyIO\Orm\Collection;
use HarmonyIO\Orm\Examples\Entity\Company;
use HarmonyIO\Orm\Examples\Entity\User;

class UserProxy extends User
{
    private $initializedProperties = [];

    /** @var int */
    private $id;

    /** @var string */
    private $name;

    /** @var string */
    private $phoneNumber;

    /** @var Company */
    private $company;

    /** @var Collection */
    private $notes;

    /** @var Collection */
    private $permissions;

    /** @var bool */
    private $isAdmin;

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPhoneNumber(): string
    {
        return $this->phoneNumber;
    }

    public function getCompany(): Company
    {
        return $this->company;
    }

    public function getNotes(): Collection
    {
        return $this->notes;
    }

    public function getPermissions(): Collection
    {
        return $this->permissions;
    }

    public function isAdmin(): bool
    {
        return $this->isAdmin;
    }
}
