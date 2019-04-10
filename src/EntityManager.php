<?php declare(strict_types=1);

namespace HarmonyIO\Orm;

use Amp\Promise;
use Amp\Sql\Link;
use Amp\Sql\ResultSet;
use Amp\Sql\Statement;
use HarmonyIO\Dbal\Connection;
use HarmonyIO\Orm\Entity\Definition\Generator\Generator;
use HarmonyIO\Orm\Entity\Entity;
use HarmonyIO\Orm\Mapping\Entity as EntityMapper;
use HarmonyIO\Orm\Mapping\Field;
use HarmonyIO\Orm\Query\Select;
use function Amp\call;

class EntityManager
{
    /** @var Connection */
    private $dbal;

    /** @var Link */
    private $link;

    /** @var Generator */
    private $definitionGenerator;

    public function __construct(Connection $dbal, Link $link, Generator $definitionGenerator)
    {
        $this->dbal                = $dbal;
        $this->link                = $link;
        $this->definitionGenerator = $definitionGenerator;
    }

    /**
     * @param mixed $id
     * @return Promise<Entity|null>
     */
    public function find(string $entity, $id): Promise
    {
        return call(function () use ($entity, $id) {
            $entityDefinition = $this->definitionGenerator->generate($entity);
            $entityMapper     = new EntityMapper($this->definitionGenerator, $entityDefinition);
            $query            = (new Select($this->dbal))->build($entityMapper, $id);

            /** @var Statement $stmt */
            $stmt = yield $this->link->prepare($query->getQuery());

            /** @var ResultSet $result */
            $result = yield $stmt->execute($query->getParameters());

            if (!yield $result->advance()) {
                return null;
            }

            return $this->createEntity($entity, $entityMapper, $result->getCurrent());
        });
    }

    /**
     * @param mixed[] $data
     */
    private function createEntity(string $entityClass, EntityMapper $entityMapper, array $data): Entity
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
