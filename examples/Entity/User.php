<?php declare(strict_types=1);

namespace HarmonyIO\Orm\Examples\Entity;

use Amp\Promise;
use HarmonyIO\Orm\Collection;
use HarmonyIO\Orm\Entity\Definition\Property\Mapping;
use HarmonyIO\Orm\Entity\Entity;

class User extends Entity
{
    /** @var Promise<int> */
    private $id;

    /** @var Promise<string> */
    private $name;

    /** @var Promise<string> */
    private $phoneNumber;

    /** @var Promise<Company> */
    private $company;

    /** @var Promise<Collection> */
    private $notes;

    /** @var Promise<Collection> */
    private $permissions;

    /** @var Promise<bool> */
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
        $this->manyToOne('company', Company::class);
        $this->oneToMany('notes', UserNote::class);
        $this->manyToMany('permissions', Permission::class);
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
     * @return Promise<string>
     */
    public function getPhoneNumber(): Promise
    {
        return $this->phoneNumber;
    }

    /**
     * @return Promise<Company>
     */
    public function getCompany(): Promise
    {
        return $this->company;
    }

    /**
     * @return Promise<Collection>
     */
    public function getNotes(): Promise
    {
        return $this->notes;
    }

    /**
     * @return Promise<Collection>
     */
    public function getPermissions(): Promise
    {
        return $this->permissions;
    }

    /**
     * @return Promise<bool>
     */
    public function isAdmin(): Promise
    {
        return $this->isAdmin;
    }
}
