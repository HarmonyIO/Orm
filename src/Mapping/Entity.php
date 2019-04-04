<?php declare(strict_types=1);

namespace HarmonyIO\Orm\Mapping;

use PhpDocReader\PhpDocReader;

class Entity
{
    /** @var PhpDocReader */
    private $docBlockReader;

    /** @var string */
    private $entityClass;

    /** @var Table */
    private $table;

    /** @var Field[]|JoinedField[] */
    private $fields = [];

    public function __construct(PhpDocReader $docBlockReader, string $entityClass)
    {
        $this->docBlockReader = $docBlockReader;
        $this->entityClass    = $entityClass;

        $reflectionClass = new \ReflectionClass($entityClass);

        $this->table = new Table($this->getTableNameFromEntity($reflectionClass), $this->generateAlias());

        $this->buildFields($reflectionClass);
    }

    private function buildFields(\ReflectionClass $entity): void
    {
        foreach ($entity->getProperties() as $property) {
            $propertyClass = $this->docBlockReader->getPropertyClass(
                new \ReflectionProperty($entity->getName(), $property->getName())
            );

            if (!$propertyClass) {
                $this->fields[] = new Field(
                    $property->getName(),
                    $this->table,
                    $property->getName(),
                    $this->generateAlias(),
                    new Type(Type::FIELD)
                );

                continue;
            }

            $joinedEntity = new Entity($this->docBlockReader, $propertyClass);

            $this->fields[] = new JoinedField(
                $property->getName(),
                $this->table,
                $this->getJoinColumn($entity, $property),
                $joinedEntity->getTable(),
                $this->getReferencedJoinColumn($entity, $property),
                $joinedEntity
            );
        }
    }

    private function getTableNameFromEntity(\ReflectionClass $entity): string
    {
        if ($entity->hasConstant('TABLE')) {
            return $entity->getConstant('TABLE');
        }

        return $this->convertPascalCaseToSnakeCase($entity->getShortName());
    }

    private function convertPascalCaseToSnakeCase(string $text): string
    {
        return trim(strtolower(preg_replace('/([A-Z])/', '_$1', $text)), '_');
    }

    private function generateAlias(): string
    {
        return bin2hex(random_bytes(16));
    }

    private function getJoinColumn(\ReflectionClass $entity, \ReflectionProperty $property): string
    {
        $docBlock = (new \ReflectionProperty($entity->getName(), $property->getName()))->getDocComment();

        preg_match('~@column\s+(.+)$~m', $docBlock, $matches);

        return trim($matches[1]);
    }

    private function getReferencedJoinColumn(\ReflectionClass $entity, \ReflectionProperty $property): string
    {
        $docBlock = (new \ReflectionProperty($entity->getName(), $property->getName()))->getDocComment();

        if (preg_match('~@referencedColumn\s+(.+)$~m', $docBlock, $matches) !== 1) {
            return 'id';
        }

        return trim($matches[1]);
    }

    public function getEntityClass(): string
    {
        return $this->entityClass;
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
