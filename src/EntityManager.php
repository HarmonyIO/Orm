<?php declare(strict_types=1);

namespace HarmonyIO\Orm;

use Amp\Promise;
use Amp\Sql\CommandResult;
use Amp\Sql\Link;
use Amp\Sql\ResultSet;
use Amp\Sql\Statement;
use Amp\Success;
use HarmonyIO\Dbal\Connection;
use HarmonyIO\Dbal\QueryBuilder\Statement\Delete as DeleteQuery;
use HarmonyIO\Dbal\QueryBuilder\Statement\Update as UpdateQuery;
use HarmonyIO\Orm\Entity\Definition\Generator\Generator;
use HarmonyIO\Orm\Entity\Entity;
use HarmonyIO\Orm\Hydrator\Hydrator;
use HarmonyIO\Orm\Mapping\Entity as EntityMapper;
use HarmonyIO\Orm\Query\Create;
use HarmonyIO\Orm\Query\Delete;
use HarmonyIO\Orm\Query\SelectAll;
use HarmonyIO\Orm\Query\SelectById;
use HarmonyIO\Orm\Query\SelectByRelation;
use HarmonyIO\Orm\Query\Update;
use function Amp\call;

class EntityManager
{
    /** @var Connection */
    private $dbal;

    /** @var Link */
    private $link;

    /** @var Generator */
    private $definitionGenerator;

    /** @var Hydrator */
    private $hydrator;

    public function __construct(Connection $dbal, Link $link, Generator $definitionGenerator, Hydrator $hydrator)
    {
        $this->dbal                = $dbal;
        $this->link                = $link;
        $this->definitionGenerator = $definitionGenerator;
        $this->hydrator            = $hydrator;
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
            $query            = (new SelectById($this->dbal))->build($entityMapper, $id);

            /** @var Statement $stmt */
            $stmt = yield $this->link->prepare($query->getQuery());

            /** @var ResultSet $result */
            $result = yield $stmt->execute($query->getParameters());

            if (!yield $result->advance()) {
                return null;
            }

            $recordSet = [];

            do {
                $recordSet[] = $result->getCurrent();
            } while (yield $result->advance());

            return $this->hydrator->createEntity($this, $entity, $entityMapper, $recordSet);
        });
    }

    /**
     * @return Promise<Entity|null>
     */
    public function refresh(Entity $entity): Promise
    {
        return $this->find(get_class($entity), $entity->getId());
    }

    /**
     * @param mixed $id
     * @return Promise<Entity|null>
     */
    public function findByRelation(string $entity, string $relatedEntity, $id): Promise
    {
        return call(function () use ($entity, $relatedEntity, $id) {
            $sourceDefinition = $this->definitionGenerator->generate($entity);
            $targetDefinition = $this->definitionGenerator->generate($relatedEntity);
            $sourceMapper     = new EntityMapper($this->definitionGenerator, $sourceDefinition);
            $targetMapper     = new EntityMapper($this->definitionGenerator, $targetDefinition);
            $query            = (new SelectByRelation($this->dbal))->build($targetMapper, $sourceMapper->getFields()['notes'], $id);

            /** @var Statement $stmt */
            $stmt = yield $this->link->prepare($query->getQuery());

            /** @var ResultSet $result */
            $result = yield $stmt->execute($query->getParameters());

            if (!yield $result->advance()) {
                return null;
            }

            $recordSet = [];

            do {
                $recordSet[] = $result->getCurrent();
            } while (yield $result->advance());

            return $this->hydrator->createCollectionFromNestedSet($this, $relatedEntity, $targetMapper, $recordSet);
        });
    }

    public function findAll(string $entity): Promise
    {
        return call(function () use ($entity) {
            $entityDefinition = $this->definitionGenerator->generate($entity);
            $entityMapper     = new EntityMapper($this->definitionGenerator, $entityDefinition);
            $query            = (new SelectAll($this->dbal))->build($entityMapper);

            /** @var Statement $stmt */
            $stmt = yield $this->link->prepare($query->getQuery());

            /** @var ResultSet $result */
            $result = yield $stmt->execute($query->getParameters());

            if (!yield $result->advance()) {
                return null;
            }

            $recordSet = [];

            do {
                $recordSet[] = $result->getCurrent();
            } while (yield $result->advance());

            return $this->hydrator->createCollectionFromNestedSet($this, $entity, $entityMapper, $recordSet);
        });
    }

    public function delete(Entity $entity): Promise
    {
        return call(function () use ($entity) {
            $entityDefinition = $this->definitionGenerator->generate(get_class($entity));
            $entityMapper     = new EntityMapper($this->definitionGenerator, $entityDefinition);
            $query            = (new Delete($this->dbal))->build($entityMapper, yield $entity->getId());

            /** @var Statement $stmt */
            $stmt = yield $this->link->prepare($query->getQuery());

            /** @var CommandResult $result */
            $result = yield $stmt->execute($query->getParameters());

            return $result->getAffectedRowCount();
        });
    }

    public function create(Entity $entity): Promise
    {
        return call(function () use ($entity) {
            $entityDefinition = $this->definitionGenerator->generate(get_class($entity));
            $entityMapper     = new EntityMapper($this->definitionGenerator, $entityDefinition);
            /** @var DeleteQuery $query */
            $query            = yield (new Create($this->dbal))->build($entity, $entityMapper);

            /** @var Statement $stmt */
            // @todo: make this db engine agnostic. amphp/mysql supports lastInsertId instead of returning
            $stmt = yield $this->link->prepare($query->getQuery() . ' RETURNING id');

            /** @var ResultSet $result */
            $result = yield $stmt->execute($query->getParameters());

            yield $result->advance();

            $closure = \Closure::bind(function () use ($result): void {
                $this->id = new Success($result->getCurrent()['id']);
            }, $entity, get_class($entity));

            $closure->call($entity);
        });
    }

    public function update(Entity $entity): Promise
    {
        return call(function() use ($entity) {
            $entityDefinition = $this->definitionGenerator->generate(get_class($entity));
            $entityMapper     = new EntityMapper($this->definitionGenerator, $entityDefinition);
            /** @var UpdateQuery $query */
            $query            = yield (new Update($this->dbal))->build($entity, $entityMapper);

            /** @var Statement $stmt */
            $stmt = yield $this->link->prepare($query->getQuery());

            /** @var CommandResult $result */
            $result = yield $stmt->execute($query->getParameters());

            return $result->getAffectedRowCount();
        });
    }
}
