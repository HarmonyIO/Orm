<?php declare(strict_types=1);

namespace HarmonyIO\Orm\Query;

use HarmonyIO\Dbal\QueryBuilder\Statement\Select as SelectQuery;
use HarmonyIO\Orm\Mapping\Entity;
use HarmonyIO\Orm\Mapping\JoinedField;

class SelectByRelation extends Select
{
    /**
     * @param mixed $value
     */
    public function build(Entity $entityMap, JoinedField $field, $value): SelectQuery
    {
        $query = $this->dbal->select(...$this->getFieldsDefinition($entityMap));
        $query = $query->from($entityMap->getTable()->getName() . ' AS ' . $entityMap->getTable()->getAlias());
        $query = $this->addJoins($query, $entityMap);

        $query = $query->leftJoin(
            $field->getTable()->getName() . ' AS ' . $field->getTable()->getAlias(),
            sprintf(
                '%s.%s = %s.%s',
                $field->getTable()->getAlias(),
                $field->getField(),
                $entityMap->getTable()->getAlias(),
                $field->getReferencedField()
            )
        );

        $condition = sprintf('%s.%s = ?', $field->getTable()->getAlias(), $field->getField());

        $query = $query->where($condition, $value);

        return $query;
    }
}
