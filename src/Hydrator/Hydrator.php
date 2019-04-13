<?php declare(strict_types=1);

namespace HarmonyIO\Orm\Hydrator;

use HarmonyIO\Orm\Collection;
use HarmonyIO\Orm\Entity\Definition\Relation\RelationType;
use HarmonyIO\Orm\Entity\Entity;
use HarmonyIO\Orm\Mapping\Entity as EntityMapper;

class Hydrator
{
    /**
     * @param mixed[] $data
     */
    public function createEntity(string $entityClass, EntityMapper $entityMapper, array $recordSet): ?Entity
    {
        $reflectionClass = new \ReflectionClass($entityClass);

        /** @var Entity $entity */
        $entity = $reflectionClass->newInstanceWithoutConstructor();

        if ($recordSet[0][$entityMapper->getFields()['id']->getAlias()] === null) {
            return null;
        }

        foreach ($entityMapper->getFields() as $field) {
            if (!$field->getProperty()->hasRelation()) {
                $this->setProperty($reflectionClass, $entity, $field->getProperty()->getName(), $recordSet[0][$field->getAlias()]);

                continue;
            }

            $relation = $field->getProperty()->getRelation();

            if ($relation->isRelationType(new RelationType(RelationType::ONE_TO_ONE))) {
                $this->setProperty(
                    $reflectionClass,
                    $entity,
                    $field->getProperty()->getName(),
                    $this->createEntity($field->getEntity()->getEntityClassName(), $field->getEntity(), $recordSet)
                );

                continue;
            }

            if ($relation->isRelationType(new RelationType(RelationType::ONE_TO_MANY))) {
                $nestedRecordSet = [];

                foreach ($recordSet as $record) {
                    if ($record[$entityMapper->getFields()['id']->getAlias()] !== $recordSet[0][$entityMapper->getFields()['id']->getAlias()]) {
                        continue;
                    }

                    $nestedRecordSet[] = $record;
                }

                $collection = $this->createCollection(
                    $field->getEntity()->getEntityClassName(),
                    $field->getEntity(),
                    $nestedRecordSet
                );

                $this->setProperty(
                    $reflectionClass,
                    $entity,
                    $field->getProperty()->getName(),
                    $collection
                );

                continue;
            }

            if ($relation->isRelationType(new RelationType(RelationType::MANY_TO_MANY))) {
                $nestedRecordSet = [];

                foreach ($recordSet as $record) {
                    if ($record[$entityMapper->getFields()['id']->getAlias()] !== $recordSet[0][$entityMapper->getFields()['id']->getAlias()]) {
                        continue;
                    }

                    $nestedRecordSet[] = $record;
                }

                $collection = $this->createCollection(
                    $field->getEntity()->getEntityClassName(),
                    $field->getEntity(),
                    $nestedRecordSet
                );

                $this->setProperty(
                    $reflectionClass,
                    $entity,
                    $field->getProperty()->getName(),
                    $collection
                );

                continue;
            }
        }

        return $entity;
    }

    /**
     * @param mixed[] $recordSet
     */
    private function createCollection(string $entityClass, EntityMapper $entityMapper, array $recordSet): Collection
    {
        $recordSets = [];

        foreach ($recordSet as $record) {
            if (!isset($recordSets[$record[$entityMapper->getFields()['id']->getAlias()]])) {
                $recordSets[$record[$entityMapper->getFields()['id']->getAlias()]] = [];
            }

            $recordSets[$record[$entityMapper->getFields()['id']->getAlias()]][] = $record;
        }

        $collection = new Collection();

        foreach ($recordSets as $nestedRecordSet) {
            $entity = $this->createEntity($entityClass, $entityMapper, $nestedRecordSet);

            if ($entity === null) {
                continue;
            }

            if (!$collection->contains($entity)) {
                $collection->add($entity);
            }
        }

        return $collection;
    }

    /**
     * @param mixed $value
     */
    private function setProperty(\ReflectionClass $reflectionClass, Entity $entity, string $property, $value): void
    {
        $property = $reflectionClass->getProperty($property);

        $property->setAccessible(true);
        $property->setValue($entity, $value);
    }
}
