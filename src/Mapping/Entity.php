<?php declare(strict_types=1);

namespace HarmonyIO\Orm\Mapping;

use HarmonyIO\Orm\Entity\Definition\Definition;
use HarmonyIO\Orm\Entity\Definition\Generator\Generator;
use HarmonyIO\Orm\Entity\Definition\Relation\ManyToMany;
use HarmonyIO\Orm\Entity\Definition\Relation\OneToMany;
use HarmonyIO\Orm\Entity\Definition\Relation\OneToOne;
use HarmonyIO\Orm\Entity\Definition\Relation\Relation;
use HarmonyIO\Orm\Entity\Definition\Relation\RelationType;

class Entity
{
    /** @var Generator */
    private $definitionGenerator;

    /** @var string */
    private $entityClassName;

    /** @var Table */
    private $table;

    /** @var Field[]|JoinedField[] */
    private $fields = [];

    public function __construct(Generator $definitionGenerator, Definition $definition)
    {
        $this->definitionGenerator = $definitionGenerator;
        $this->entityClassName     = $definition->getEntityClassName();

        $this->table = new Table($definition->getTableName(), $this->generateAlias());

        $this->buildFields($definition);
    }

    private function buildFields(Definition $definition): void
    {
        foreach ($definition->getProperties() as $property) {
            if (!$property->hasRelation()) {
                $this->fields[$property->getName()] = new Field(
                    $property,
                    $this->table,
                    $property->getColumn(),
                    $this->generateAlias(),
                    new Type(Type::FIELD)
                );

                continue;
            }

            $joinedEntity = new Entity(
                $this->definitionGenerator,
                $this->definitionGenerator->generate($property->getRelation()->getEntityClassName())
            );

            $joinedField = $property->getColumn();

            $relation = $property->getRelation();

            if ($relation->isRelationType(new RelationType(RelationType::ONE_TO_ONE))) {
                /** @var OneToOne $relation */
                $this->fields[$property->getName()] = new JoinedField(
                    $property,
                    $this->table,
                    $property->getColumn(),
                    $joinedEntity->getTable(),
                    $relation->getForeignKey(),
                    $joinedEntity
                );

                continue;
            }

            if ($relation->isRelationType(new RelationType(RelationType::ONE_TO_MANY))) {
                /** @var OneToMany $relation */
                $this->fields[$property->getName()] = new JoinedField(
                    $property,
                    $this->table,
                    $relation->getLocalKey(),
                    $joinedEntity->getTable(),
                    $relation->getForeignKey(),
                    $joinedEntity
                );

                continue;
            }

            $linkTable = null;

            if ($property->getRelation()->isRelationType(new RelationType(RelationType::MANY_TO_MANY))) {
                /** @var ManyToMany $relation */
                $linkTable = new Table($relation->getLinkTableName(), $this->generateAlias());

                $this->fields[$property->getName()] = new JoinedField(
                    $property,
                    $this->table,
                    $joinedField,
                    $joinedEntity->getTable(),
                    $relation->getForeignKey(),
                    $joinedEntity,
                    $linkTable
                );

                continue;
            }

            throw new \Exception('Unknown relation type.');
        }
    }

    private function generateAlias(): string
    {
        return bin2hex(random_bytes(16));
    }

    public function getEntityClassName(): string
    {
        return $this->entityClassName;
    }

    /**
     * @return Field[]|JoinedField[]
     */
    public function getFields(): array
    {
        return $this->fields;
    }

    public function getTable(): Table
    {
        return $this->table;
    }
}
