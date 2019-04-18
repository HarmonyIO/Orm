<?php declare(strict_types=1);

namespace HarmonyIO\Orm\Query;

use HarmonyIO\Dbal\Connection;
use HarmonyIO\Dbal\QueryBuilder\Statement\Select as SelectQuery;
use HarmonyIO\Orm\Entity\Definition\Relation\ManyToMany;
use HarmonyIO\Orm\Entity\Definition\Relation\RelationType;
use HarmonyIO\Orm\Mapping\Entity;

abstract class Select
{
    /** @var Connection */
    protected $dbal;

    public function __construct(Connection $dbal)
    {
        $this->dbal = $dbal;
    }

    /**
     * @return string[]
     */
    protected function getFieldsDefinition(Entity $entityMap): array
    {
        $fieldsMapping = $entityMap->getFields();

        $fields = [];

        foreach ($fieldsMapping as $field) {
            if (!$field->getProperty()->hasRelation()) {
                $fields[] = sprintf('%s.%s AS %s', $field->getTable()->getAlias(), $field->getField(), $field->getAlias());

                continue;
            }

            $relation = $field->getProperty()->getRelation();

            if ($relation->isRelationType(new RelationType(RelationType::ONE_TO_MANY))) {
                continue;
            }

            if ($relation->isRelationType(new RelationType(RelationType::MANY_TO_ONE))) {
                $fields = array_merge($fields, $this->getFieldsDefinition($field->getEntity()));

                continue;
            }

            // one side needs lazy loading
            if ($relation->isRelationType(new RelationType(RelationType::ONE_TO_ONE))) {
                $fields = array_merge($fields, $this->getFieldsDefinition($field->getEntity()));

                continue;
            }

            // needs lazy loading
            if ($relation->isRelationType(new RelationType(RelationType::MANY_TO_MANY))) {
                $fields = array_merge($fields, $this->getFieldsDefinition($field->getEntity()));

                continue;
            }
        }

        return $fields;
    }

    protected function addJoins(SelectQuery $query, Entity $entityMap): SelectQuery
    {
        foreach ($entityMap->getFields() as $field) {
            if (!$field->getProperty()->hasRelation()) {
                continue;
            }

            $relation = $field->getProperty()->getRelation();

            if ($relation->isRelationType(new RelationType(RelationType::ONE_TO_MANY))) {
                $query->leftJoin(
                    $field->getReferencedTable()->getName() . ' AS ' .$field->getReferencedTable()->getAlias(),
                    sprintf(
                        '%s.%s = %s.%s',
                        $field->getReferencedTable()->getAlias(),
                        $relation->getForeignKey(),
                        $field->getTable()->getAlias(),
                        $relation->getLocalKey()
                    )
                );

                continue;
            }

            if ($relation->isRelationType(new RelationType(RelationType::MANY_TO_MANY))) {
                /** @var ManyToMany $relation */
                $query->leftJoin(
                    $field->getLinkTable()->getName() . ' AS ' . $field->getLinkTable()->getAlias(),
                    sprintf(
                        '%s.%s = %s.%s',
                        $field->getLinkTable()->getAlias(),
                        $relation->getLocalLink(),
                        $field->getTable()->getAlias(),
                        $relation->getLocalKey()
                    )
                );

                $query->leftJoin(
                    $field->getReferencedTable()->getName() . ' AS ' . $field->getReferencedTable()->getAlias(),
                    sprintf(
                        '%s.%s = %s.%s',
                        $field->getReferencedTable()->getAlias(),
                        $relation->getForeignKey(),
                        $field->getLinkTable()->getAlias(),
                        $relation->getForeignLink()
                    )
                );

                $query = $this->addJoins($query, $field->getEntity());

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
