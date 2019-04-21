<?php declare(strict_types=1);

namespace HarmonyIO\Orm\Query;

use function Amp\call;
use Amp\Promise;
use Amp\Success;
use HarmonyIO\Dbal\Connection;
use HarmonyIO\Dbal\QueryBuilder\Statement\Delete as DeleteQuery;
use HarmonyIO\Orm\Entity\Definition\Relation\RelationType;
use HarmonyIO\Orm\Entity\Entity;
use HarmonyIO\Orm\Mapping\Entity as EntityMapper;

class Create
{
    /** @var Connection */
    private $dbal;

    public function __construct(Connection $dbal)
    {
        $this->dbal = $dbal;
    }

    /**
     * @return Promise<DeleteQuery>
     */
    public function build(Entity $entity, EntityMapper $entityMap): Promise
    {
        return call(function () use ($entity, $entityMap) {
            $reflectedEntity = new \ReflectionObject($entity);

            $query = $this->dbal->insert($entityMap->getTable()->getName());

            foreach ($entityMap->getFields() as $field) {
                if($field->getProperty()->getName() === 'id') {
                    continue;
                }

                if (!$field->getProperty()->hasRelation()) {
                    $query = $query->value(
                        $field->getField(),
                        yield $this->getValue($entity, $reflectedEntity, $field->getProperty()->getName())
                    );

                    continue;
                }

                $relation = $field->getProperty()->getRelation();

                if ($relation->isRelationType(new RelationType(RelationType::MANY_TO_ONE))) {
                    $relatedEntity = yield $this->getValue($entity, $reflectedEntity, $field->getProperty()->getName());

                    $query = $query->value(
                        $field->getField(),
                        yield $relatedEntity->getId()
                    );

                    continue;
                }
            }

            return $query;
        });
    }

    /**
     * @return Promise<mixed>
     */
    private function getValue(Entity $entity, \ReflectionObject $reflectedEntity, string $propertyName): Promise
    {
        $property = $reflectedEntity->getProperty($propertyName);
        $property->setAccessible(true);

        $value = $property->getValue($entity);

        if ($value === null) {
            return new Success($value);
        }

        return $value;
    }
}
