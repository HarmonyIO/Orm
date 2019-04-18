<?php declare(strict_types=1);

namespace HarmonyIO\Orm\Mapping;

use HarmonyIO\Orm\Entity\Definition\Definition;

class LazyEntity
{
    /** @var string */
    private $entityClassName;

    /** @var Table */
    private $table;

    public function __construct(Definition $definition)
    {
        $this->entityClassName = $definition->getEntityClassName();
        $this->table           = new Table($definition->getTableName(), $this->generateAlias());
    }

    private function generateAlias(): string
    {
        return bin2hex(random_bytes(16));
    }

    public function getEntityClassName(): string
    {
        return $this->entityClassName;
    }

    public function getTable(): Table
    {
        return $this->table;
    }
}
