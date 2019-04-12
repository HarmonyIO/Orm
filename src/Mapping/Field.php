<?php declare(strict_types=1);

namespace HarmonyIO\Orm\Mapping;

use HarmonyIO\Orm\Entity\Definition\Property\Property;

class Field
{
    /** @var Property */
    private $property;

    /** @var Table */
    private $table;

    /** @var string */
    private $field;

    /** @var string */
    private $alias;

    /** @var Type */
    private $type;

    public function __construct(Property $property, Table $table, string $field, string $alias, Type $type)
    {
        $this->property = $property;
        $this->table    = $table;
        $this->field    = $field;
        $this->alias    = $alias;
        $this->type     = $type;
    }

    public function getProperty(): Property
    {
        return $this->property;
    }

    public function getTable(): Table
    {
        return $this->table;
    }

    public function getField(): string
    {
        return $this->field;
    }

    public function getAlias(): string
    {
        return $this->alias;
    }

    public function getType(): Type
    {
        return $this->type;
    }
}
