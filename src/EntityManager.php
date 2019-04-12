<?php declare(strict_types=1);

namespace HarmonyIO\Orm;

use Amp\Promise;
use Amp\Sql\Link;
use Amp\Sql\ResultSet;
use Amp\Sql\Statement;
use HarmonyIO\Dbal\Connection;
use HarmonyIO\Orm\Entity\Definition\Generator\Generator;
use HarmonyIO\Orm\Entity\Entity;
use HarmonyIO\Orm\Hydrator\Hydrator;
use HarmonyIO\Orm\Mapping\Entity as EntityMapper;
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
            $query            = (new Select($this->dbal))->build($entityMapper, $id);

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

            return $this->hydrator->createEntity($entity, $entityMapper, $recordSet);
        });
    }
}
