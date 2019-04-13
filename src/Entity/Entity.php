<?php declare(strict_types=1);

namespace HarmonyIO\Orm\Entity;

use Doctrine\Common\Inflector\Inflector;
use HarmonyIO\Orm\Entity\Definition\Relation\LoadType;
use HarmonyIO\Orm\Entity\Definition\Relation\ManyToMany;
use HarmonyIO\Orm\Entity\Definition\Relation\OneToMany;
use HarmonyIO\Orm\Entity\Definition\Relation\OneToOne;
use HarmonyIO\Orm\Entity\Definition\Relation\Relation;

abstract class Entity
{
    /** @var Relation[] */
    private $relations = [];

    protected function oneToOne(string $propertyName, string $entityClass, string $foreignKey = 'id', ?string $localKey = null): void
    {
        if ($localKey === null) {
            $localKey = Inflector::tableize((new \ReflectionClass($entityClass))->getShortName()) . '_id';
        }

        $this->relations[$propertyName] = new OneToOne(
            new LoadType(LoadType::EAGER),
            $entityClass,
            $foreignKey,
            $localKey
        );
    }

    protected function oneToMany(string $propertyName, string $entityClass, ?string $foreignKey = null, string $localKey = 'id'): void
    {
        if ($foreignKey === null) {
            $foreignKey = Inflector::tableize((new \ReflectionClass(static::class))->getShortName()) . '_id';
        }

        $this->relations[$propertyName] = new OneToMany(
            new LoadType(LoadType::EAGER),
            $entityClass,
            $foreignKey,
            $localKey
        );
    }

    protected function manyToMany(
        string $propertyName,
        string $entityClass,
        ?string $linkTableName = null,
        string $foreignKey = 'id',
        ?string $foreignLink = null,
        string $localKey = 'id',
        ?string $localLink = null
    ): void {
        if ($linkTableName === null) {
            $linkTableName = sprintf(
                '%s_%s',
                Inflector::tableize(Inflector::pluralize((new \ReflectionClass(static::class))->getShortName())),
                Inflector::tableize(Inflector::pluralize((new \ReflectionClass($entityClass))->getShortName()))
            );
        }

        if ($foreignLink === null) {
            $foreignLink = Inflector::tableize((new \ReflectionClass($entityClass))->getShortName()) . '_id';
        }

        if ($localLink === null) {
            $localLink = Inflector::tableize((new \ReflectionClass(static::class))->getShortName()) . '_id';
        }

        $this->relations[$propertyName] = new ManyToMany(
            new LoadType(LoadType::EAGER),
            $entityClass,
            $linkTableName,
            $foreignKey,
            $foreignLink,
            $localKey,
            $localLink
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
