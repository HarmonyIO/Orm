<?php declare(strict_types=1);

namespace HarmonyIO\Orm\Entity\Definition;

use Doctrine\Common\Inflector\Inflector;
use HarmonyIO\Orm\Entity\Definition\Property\MappingCollection;
use HarmonyIO\Orm\Entity\Definition\Property\Property;
use HarmonyIO\Orm\Entity\Definition\Property\PropertyCollection;
use HarmonyIO\Orm\Entity\Entity;

class Definition
{
    /** @var string */
    private $entityClassName;

    /** @var string */
    private $tableName;

    /** @var PropertyCollection */
    private $properties;

    public function __construct(string $entityClassName)
    {
        $this->entityClassName = $entityClassName;

        $entityReflection = new \ReflectionClass($entityClassName);
        /** @var Entity $entityInstance */
        $entityInstance = $entityReflection->newInstance();

        $this->tableName  = $this->generateTableName($entityReflection, $entityInstance);
        $this->properties = $this->generateProperties($entityReflection, $entityInstance);
    }

    private function generateTableName(\ReflectionClass $entityReflection, Entity $entityInstance): string
    {
        if ($entityReflection->hasMethod('table')) {
            $tableMethod = $entityReflection->getMethod('table');

            $tableMethod->setAccessible(true);

            return $tableMethod->invoke($entityInstance);
        }

        return $this->getTableNameFromEntityClassName($entityReflection);
    }

    private function getTableNameFromEntityClassName(\ReflectionClass $entityReflection): string
    {
        return Inflector::pluralize(Inflector::tableize($entityReflection->getShortName()));
    }

    private function generateProperties(\ReflectionClass $entityReflection, Entity $entityInstance): PropertyCollection
    {
        if ($entityReflection->hasMethod('relate')) {
            $relateMethod = $entityReflection->getMethod('relate');

            $relateMethod->setAccessible(true);

            $relateMethod->invoke($entityInstance);
        }

        if (!$entityReflection->hasMethod('propertyMapping')) {
            return $this->createProperties($entityReflection, $entityInstance, new MappingCollection());
        }

        $mappingMethod = $entityReflection->getMethod('propertyMapping');

        $mappingMethod->setAccessible(true);

        return $this->createProperties(
            $entityReflection,
            $entityInstance,
            new MappingCollection(...$mappingMethod->invoke($entityInstance))
        );
    }

    private function createProperties(
        \ReflectionClass $entityReflection,
        Entity $entityInstance,
        MappingCollection $mappingCollection
    ): PropertyCollection {
        $properties = [];

        foreach ($entityReflection->getProperties() as $property) {
            if (!$entityInstance->propertyHasRelation($property->getName())) {
                $properties[] = new Property($property->getName(), $mappingCollection->getColumnName($property->getName()));

                continue;
            }

            $propertyName = $property->getName() . '_id';

            if ($mappingCollection->hasCustomColumn($property->getName())) {
                $propertyName = $mappingCollection->getColumnName($property->getName());
            }

            $properties[] = new Property(
                $property->getName(),
                $propertyName,
                $entityInstance->getPropertyRelation($property->getName())
            );
        }

        return new PropertyCollection(...$properties);
    }

    public function getEntityClassName(): string
    {
        return $this->entityClassName;
    }

    public function getTableName(): string
    {
        return $this->tableName;
    }

    public function getProperties(): PropertyCollection
    {
        return $this->properties;
    }
}
