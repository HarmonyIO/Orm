<?php declare(strict_types=1);

namespace HarmonyIO\Orm\Entity\Definition\Property;

class Mapping
{
    /** @var string */
    private $propertyName;

    /** @var string */
    private $columnName;

    public function __construct(string $propertyName, string $columnName)
    {
        $this->propertyName = $propertyName;
        $this->columnName   = $columnName;
    }

    public function getPropertyName(): string
    {
        return $this->propertyName;
    }

    public function getColumnName(): string
    {
        return $this->columnName;
    }
}
