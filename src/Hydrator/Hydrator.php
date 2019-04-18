<?php declare(strict_types=1);

namespace HarmonyIO\Orm\Hydrator;

use Amp\Promise;
use Amp\Success;
use HarmonyIO\Orm\Collection;
use HarmonyIO\Orm\Entity\Definition\Relation\Relation;
use HarmonyIO\Orm\Entity\Definition\Relation\RelationType;
use HarmonyIO\Orm\Entity\Entity;
use HarmonyIO\Orm\EntityManager;
use HarmonyIO\Orm\Mapping\Entity as EntityMapper;

class Hydrator
{
    /**
     * @param mixed[] $recordSet
     */
    public function createEntity(EntityManager $em, string $entityClass, EntityMapper $entityMapper, array $recordSet): ?Entity
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
                    $this->createEntity($em, $field->getEntity()->getEntityClassName(), $field->getEntity(), $recordSet)
                );

                continue;
            }

            if ($relation->isRelationType(new RelationType(RelationType::MANY_TO_ONE))) {
                $this->setProperty(
                    $reflectionClass,
                    $entity,
                    $field->getProperty()->getName(),
                    $this->createEntity($em, $field->getEntity()->getEntityClassName(), $field->getEntity(), $recordSet)
                );

                continue;
            }

            if ($relation->isRelationType(new RelationType(RelationType::ONE_TO_MANY))) {
                $this->setProperty(
                    $reflectionClass,
                    $entity,
                    $field->getProperty()->getName(),
                    $em->findByRelation(
                        $entityClass,
                        $relation->getEntityClassName(),
                        $this->getIdentifierValueForRelation($relation, $entityMapper, $recordSet[0])
                    )
                );

                /*
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
                */
            }

            if ($relation->isRelationType(new RelationType(RelationType::MANY_TO_MANY))) {
                $nestedRecordSet = [];

                foreach ($recordSet as $record) {
                    if ($record[$entityMapper->getFields()['id']->getAlias()] !== $recordSet[0][$entityMapper->getFields()['id']->getAlias()]) {
                        continue;
                    }

                    $nestedRecordSet[] = $record;
                }

                $collection = $this->createCollectionFromNestedSet(
                    $em,
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
    public function createCollectionFromNestedSet(EntityManager $em, string $entityClass, EntityMapper $entityMapper, array $recordSet): Collection
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
            $entity = $this->createEntity($em, $entityClass, $entityMapper, $nestedRecordSet);

            if ($entity === null) {
                continue;
            }

            if ($collection->contains($entity)) {
                continue;
            }

            $collection->add($entity);
        }

        return $collection;
    }

    /**
     * @param mixed[] $recordSet
     */
    public function createCollection(EntityManager $em, string $entityClass, EntityMapper $entityMapper, array $recordSet): Collection
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
            $entity = $this->createEntity($em, $entityClass, $entityMapper, $nestedRecordSet);

            if ($entity === null) {
                continue;
            }

            if ($collection->contains($entity)) {
                continue;
            }

            $collection->add($entity);
        }

        return $collection;
    }

    /**
     * @param mixed $value
     */
    private function setProperty(\ReflectionClass $reflectionClass, Entity $entity, string $property, $value): void
    {
        if (!$value instanceof Promise) {
            $value = new Success($value);
        }

        $property = $reflectionClass->getProperty($property);

        $property->setAccessible(true);
        $property->setValue($entity, $value);
    }

    /**
     * @param mixed[] $record
     * @return mixed
     */
    private function getIdentifierValueForRelation(Relation $relation, EntityMapper $entityMapper, array $record)
    {
        $fieldAlias = $entityMapper->getFields()[$relation->getLocalKey()]->getAlias();

        return $record[$fieldAlias];
    }
}
