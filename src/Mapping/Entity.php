<?php declare(strict_types=1);

namespace HarmonyIO\Orm\Mapping;

use HarmonyIO\Orm\Entity\Definition\Definition;
use HarmonyIO\Orm\Entity\Definition\Generator\Generator;
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

            if ($property->getRelation()->getRelationType()->getValue() === RelationType::HAS_MANY) {
                $joinedField = $property->getRelation()->getLocalKey();
            }

            $this->fields[$property->getName()] = new JoinedField(
                $property,
                $this->table,
                $joinedField,
                $joinedEntity->getTable(),
                $property->getRelation()->getForeignKey(),
                $joinedEntity
            );
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
