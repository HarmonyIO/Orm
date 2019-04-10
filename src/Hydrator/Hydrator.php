<?php declare(strict_types=1);

namespace HarmonyIO\Orm\Hydrator;

use HarmonyIO\Orm\Entity\Entity;
use HarmonyIO\Orm\Mapping\Entity as EntityMapper;
use HarmonyIO\Orm\Mapping\Field;

class Hydrator
{
    /**
     * @param mixed[] $data
     */
    public function createEntity(string $entityClass, EntityMapper $entityMapper, array $data): Entity
    {
        $reflectionClass = new \ReflectionClass($entityClass);

        /** @var Entity $entity */
        $entity = $reflectionClass->newInstanceWithoutConstructor();

        foreach ($entityMapper->getFields() as $field) {
            if ($field instanceof Field) {
                $this->setProperty($reflectionClass, $entity, $field->getProperty(), $data[$field->getAlias()]);

                continue;
            }

            $this->setProperty(
                $reflectionClass,
                $entity,
                $field->getProperty(),
                $this->createEntity($field->getEntity()->getEntityClassName(), $field->getEntity(), $data)
            );
        }

        return $entity;
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
