<?php declare(strict_types=1);

namespace HarmonyIO\Orm\Entity;

class User
{
    private const TABLE = 'users';

    /** @var int */
    private $id;

    /** @var string */
    private $name;

    /**
     * @var Company
     * @column company_id
     */
    private $company;

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getCompany(): Company
    {
        return $this->company;
    }
}
