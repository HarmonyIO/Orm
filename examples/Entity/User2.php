<?php declare(strict_types=1);

namespace HarmonyIO\Orm\Examples\Entity;

use HarmonyIO\Orm\Entity\Definition\Property\Mapping;
use HarmonyIO\Orm\Entity\Entity;

class User2 extends Entity
{
    /** @var int */
    private $id;

    /** @var string */
    private $name;

    /** @var string */
    private $phoneNumber;

    /** @var Company */
    private $company;

    /** @var bool */
    private $isAdmin;

    private $notes;

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
        $this->hasOne('company', Company::class);
        $this->hasMany('notes', UserNote::class);
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

    public function isAdmin(): bool
    {
        return $this->isAdmin;
    }
}
