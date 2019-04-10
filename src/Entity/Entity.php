<?php declare(strict_types=1);

namespace HarmonyIO\Orm\Entity;

use HarmonyIO\Orm\Entity\Definition\Relation\LoadType;
use HarmonyIO\Orm\Entity\Definition\Relation\Relation;
use HarmonyIO\Orm\Entity\Definition\Relation\RelationType;

abstract class Entity
{
    /** @var Relation[] */
    private $relations = [];

    protected function hasOne(string $propertyName, string $entityClass, string $foreignKey = 'id'): void
    {
        $this->relations[$propertyName] = new Relation(
            new RelationType(RelationType::HAS_ONE),
            new LoadType(LoadType::EAGER),
            $entityClass,
            $foreignKey
        );
    }

    // @todo: make private
    public function propertyHasRelation(string $propertyName): bool
    {
        return isset($this->relations[$propertyName]);
    }

    // @todo: make private
    public function getPropertyRelation(string $propertyName): ?Relation
    {
        if (!$this->propertyHasRelation($propertyName)) {
            return null;
        }

        return $this->relations[$propertyName];
    }
}
