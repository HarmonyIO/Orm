<?php declare(strict_types=1);

namespace HarmonyIO\Orm;

use Amp\Promise;
use Amp\Sql\Link;
use Amp\Sql\ResultSet;
use Amp\Sql\Statement;
use HarmonyIO\Dbal\Connection;
use HarmonyIO\Dbal\QueryBuilder\Statement\Select;
use HarmonyIO\Orm\Mapping\Entity;
use HarmonyIO\Orm\Mapping\Field;
use HarmonyIO\Orm\Mapping\JoinedField;
use PhpDocReader\PhpDocReader;
use function Amp\call;

class EntityManager
{
    /** @var Connection */
    private $dbal;

    /** @var Link */
    private $link;

    public function __construct(Connection $dbal, Link $link)
    {
        $this->dbal = $dbal;
        $this->link = $link;
    }

    /**
     * @param mixed $id
     */
    public function find(string $entity, $id): Promise
    {
        return call(function () use ($entity, $id) {
            $entityMapper = new Entity(new PhpDocReader(), $entity);

            $query = $this->dbal->select(...$this->getFieldsDefinition($entityMapper));
            $query = $query->from($entityMapper->getTable()->getName() . ' AS ' . $entityMapper->getTable()->getAlias());
            $query = $this->addJoins($query, $entityMapper);
            $query = $query->where($entityMapper->getTable()->getAlias() . '.id = ?', $id);

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
     * @return string[]
     */
    private function getFieldsDefinition(Entity $entityMapper): array
    {
        $fieldsMapping = $entityMapper->getFields();

        $fields = [];

        foreach ($fieldsMapping as $field) {
            if ($field instanceof Field) {
                $fields[] = sprintf('%s.%s AS %s', $field->getTable()->getAlias(), $field->getField(), $field->getAlias());
            } else {
                $fields = array_merge($fields, $this->getFieldsDefinition($field->getEntity()));
            }
        }

        return $fields;
    }

    private function addJoins(Select $query, Entity $entityMapper): Select
    {
        foreach ($entityMapper->getFields() as $field) {
            if (!($field instanceof JoinedField)) {
                continue;
            }

            $query->join(
                $field->getReferencedTable()->getName() . ' AS ' . $field->getReferencedTable()->getAlias(),
                sprintf(
                    '%s.%s = %s.%s',
                    $field->getReferencedTable()->getAlias(),
                    $field->getReferencedField(),
                    $field->getTable()->getAlias(),
                    $field->getField()
                )
            );
        }

        return $query;
    }

    /**
     * @param mixed[] $data
     * @return object
     */
    private function createEntity(string $entityClass, Entity $entityMapper, array $data)
    {
        $reflectionClass = new \ReflectionClass($entityClass);

        $entity = $reflectionClass->newInstanceWithoutConstructor();

        foreach ($entityMapper->getFields() as $field) {
            if ($field instanceof Field) {
                $this->setProperty($reflectionClass, $entity, $field->getProperty(), $data[$field->getAlias()]);
            }

            if ($field instanceof JoinedField) {
                $this->setProperty(
                    $reflectionClass,
                    $entity,
                    $field->getProperty(),
                    $this->createEntity($field->getEntity()->getEntityClass(), $field->getEntity(), $data)
                );
            }
        }

        return $entity;
    }

    private function setProperty(\ReflectionClass $reflectionClass, $entity, string $property, $value): void
    {
        $property = $reflectionClass->getProperty($property);

        $property->setAccessible(true);
        $property->setValue($entity, $value);
    }
}
