<?php declare(strict_types=1);

namespace HarmonyIO\Orm\Examples\Entity;

use HarmonyIO\Orm\Collection;
use HarmonyIO\Orm\Entity\Definition\Property\Mapping;
use HarmonyIO\Orm\Entity\Entity;

class User extends Entity
{
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

    protected function table(): string
    {
        return 'members';
    }

    /**
     * @return Mapping[]
     */
    protected function propertyMapping(): array
    {
        return [
            new Mapping('phoneNumber', 'phone'),
        ];
    }

    protected function relate(): void
    {
        $this->oneToOne('company', Company::class);
        $this->oneToMany('notes', UserNote::class);
        $this->manyToMany('permissions', Permission::class);
    }

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
