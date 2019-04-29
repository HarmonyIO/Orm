<?php declare(strict_types=1);

namespace HarmonyIO\Orm\Examples\Entity;

use Amp\Promise;
use Amp\Success;
use HarmonyIO\Orm\Collection;
use HarmonyIO\Orm\Entity\Definition\Property\Mapping;
use HarmonyIO\Orm\Entity\Entity;
use function Amp\call;

class User extends Entity
{
    /** @var Promise<int> */
    protected $id;

    /** @var Promise<string> */
    protected $name;

    /** @var Promise<string> */
    protected $phoneNumber;

    /** @var Promise<Company> */
    protected $company;

    /** @var Promise<Collection> */
    protected $notes;

    /** @var Promise<Collection> */
    protected $permissions;

    /** @var Promise<bool> */
    protected $isAdmin;

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

    public function __construct()
    {
        $this->notes       = new Collection();
        $this->permissions = new Collection();
        $this->isAdmin     = new Success(false);
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
     * @return Promise<User>
     */
    public function setName(string $name): Promise
    {
        $this->name = new Success($name);

        $this->markPropertyAsChanged('name');

        return new Success($this);
    }

    /**
     * @return Promise<string>
     */
    public function getPhoneNumber(): Promise
    {
        return $this->phoneNumber;
    }

    /**
     * @return Promise<User>
     */
    public function setPhoneNumber(string $phoneNumber): Promise
    {
        $this->phoneNumber = new Success($phoneNumber);

        $this->markPropertyAsChanged('phoneNumber');

        return new Success($this);
    }

    /**
     * @return Promise<Company>
     */
    public function getCompany(): Promise
    {
        return $this->company;
    }

    /**
     * @return Promise<User>
     */
    public function setCompany(Company $company): Promise
    {
        $this->company = new Success($company);

        $this->markPropertyAsChanged('company');

        return new Success($this);
    }

    /**
     * @return Promise<Collection>
     */
    public function getNotes(): Promise
    {
        return $this->notes;
    }

    /**
     * @return Promise<User>
     */
    public function setNotes(Collection $notes): Promise
    {
        $this->notes = new Success($notes);

        $this->markPropertyAsChanged('notes');

        return new Success($this);
    }

    /**
     * @return Promise<User>
     */
    public function addNotes(UserNote ...$notes): Promise
    {
        return call(function () use ($notes) {
            if ($this->notes === null) {
                $this->notes = new Success(new Collection());
            }

            /** @var Collection $collection */
            $collection = yield $this->notes;

            foreach ($notes as $note) {
                $collection->add($note);
            }

            $this->notes = new Success($collection);

            $this->markPropertyAsChanged('notes');

            return $this;
        });
    }

    /**
     * @return Promise<User>
     */
    public function removeNotes(UserNote ...$notes): Promise
    {
        return call(function () use ($notes) {
            if ($this->notes === null) {
                $this->notes = new Success(new Collection());
            }

            /** @var Collection $collection */
            $collection = yield $this->notes;

            foreach ($notes as $note) {
                $collection->remove($note);
            }

            $this->notes = new Success($collection);

            $this->markPropertyAsChanged('notes');

            return $this;
        });
    }

    /**
     * @return Promise<Collection>
     */
    public function getPermissions(): Promise
    {
        return $this->permissions;
    }

    /**
     * @return Promise<User>
     */
    public function setPermissions(Collection $permissions): Promise
    {
        $this->permissions = new Success($permissions);

        $this->permissions->markAsChanged();

        return new Success($this);
    }

    /**
     * @return Promise<User>
     */
    public function addPermissions(Permission ...$permissions): Promise
    {
        return call(function () use ($permissions) {
            if ($this->permissions === null) {
                $this->permissions = new Success(new Collection());
            }

            /** @var Collection $collection */
            $collection = yield $this->permissions;

            foreach ($permissions as $permission) {
                $collection->add($permission);
            }

            $this->permissions = new Success($collection);

            $this->permissions->markAsChanged();

            return $this;
        });
    }

    /**
     * @return Promise<User>
     */
    public function removePermissions(Permission ...$permissions): Promise
    {
        return call(function () use ($permissions) {
            if ($this->permissions === null) {
                $this->permissions = new Success(new Collection());
            }

            /** @var Collection $collection */
            $collection = yield $this->permissions;

            foreach ($permissions as $permission) {
                $collection->remove($permission);
            }

            $this->permissions->markAsChanged();

            $this->markPropertyAsChanged('permissions');

            return $this;
        });
    }

    /**
     * @return Promise<bool>
     */
    public function isAdmin(): Promise
    {
        return $this->isAdmin;
    }

    public function setAdmin(bool $isAdmin): Promise
    {
        $this->isAdmin = new Success($isAdmin);

        $this->markPropertyAsChanged('isAdmin');

        return new Success($this);
    }
}
