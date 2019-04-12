<?php declare(strict_types=1);

namespace HarmonyIO\Orm\Query;

use HarmonyIO\Dbal\Connection;
use HarmonyIO\Dbal\QueryBuilder\Statement\Select as SelectQuery;
use HarmonyIO\Orm\Mapping\Entity;
use HarmonyIO\Orm\Mapping\Field;
use HarmonyIO\Orm\Mapping\JoinedField;

class Select
{
    /** @var Connection */
    private $dbal;

    public function __construct(Connection $dbal)
    {
        $this->dbal = $dbal;
    }

    public function build(Entity $entityMap, int $id): SelectQuery
    {
        $query = $this->dbal->select(...$this->getFieldsDefinition($entityMap));
        $query = $query->from($entityMap->getTable()->getName() . ' AS ' . $entityMap->getTable()->getAlias());
        $query = $this->addJoins($query, $entityMap);
        $query = $query->where($entityMap->getTable()->getAlias() . '.id = ?', $id);

        return $query;
    }

    /**
     * @return string[]
     */
    private function getFieldsDefinition(Entity $entityMap): array
    {
        $fieldsMapping = $entityMap->getFields();

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

    private function addJoins(SelectQuery $query, Entity $entityMap): SelectQuery
    {
        foreach ($entityMap->getFields() as $field) {
            if (!($field instanceof JoinedField)) {
                continue;
            }

            $query->leftJoin(
                $field->getReferencedTable()->getName() . ' AS ' . $field->getReferencedTable()->getAlias(),
                sprintf(
                    '%s.%s = %s.%s',
                    $field->getReferencedTable()->getAlias(),
                    $field->getReferencedField(),
                    $field->getTable()->getAlias(),
                    $field->getField()
                )
            );

            $query = $this->addJoins($query, $field->getEntity());
        }

        return $query;
    }
}
