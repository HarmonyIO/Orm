<?php declare(strict_types=1);

namespace HarmonyIO\Orm\Query;

use HarmonyIO\Dbal\QueryBuilder\Statement\Select as SelectQuery;
use HarmonyIO\Orm\Mapping\Entity;

class SelectById extends Select
{
    public function build(Entity $entityMap, int $id): SelectQuery
    {
        $query = $this->dbal->select(...$this->getFieldsDefinition($entityMap));
        $query = $query->from($entityMap->getTable()->getName() . ' AS ' . $entityMap->getTable()->getAlias());
        $query = $this->addJoins($query, $entityMap);
        $query = $query->where($entityMap->getTable()->getAlias() . '.id = ?', $id);

        return $query;
    }
}
