<?php declare(strict_types=1);

namespace HarmonyIO\Orm\Query;

use HarmonyIO\Dbal\Connection;
use HarmonyIO\Dbal\QueryBuilder\Statement\Delete as DeleteQuery;
use HarmonyIO\Orm\Mapping\Entity as EntityMapper;

class Delete
{
    /** @var Connection */
    private $dbal;

    public function __construct(Connection $dbal)
    {
        $this->dbal = $dbal;
    }

    /**
     * @param mixed $id
     */
    public function build(EntityMapper $entityMap, $id): DeleteQuery
    {
        $query = $this->dbal->delete($entityMap->getTable()->getName() . ' AS ' . $entityMap->getTable()->getAlias());
        $query = $query->where($entityMap->getTable()->getAlias() . '.id = ?', $id);

        return $query;
    }
}
