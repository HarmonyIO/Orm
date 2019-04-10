<?php declare(strict_types=1);

namespace HarmonyIO\Orm\Entity\Definition\Property;

use Doctrine\Common\Inflector\Inflector;

class MappingCollection
{
    /** @var Mapping[] */
    private $mappings = [];

    public function __construct(Mapping ...$mappings)
    {
        foreach ($mappings as $mapping) {
            $this->mappings[$mapping->getPropertyName()] = $mapping;
        }
    }

    public function hasCustomColumn(string $propertyName): bool
    {
        return isset($this->mappings[$propertyName]);
    }

    public function getColumnName(string $propertyName): string
    {
        if ($this->hasCustomColumn($propertyName)) {
            return $this->mappings[$propertyName]->getColumnName();
        }

        return Inflector::tableize($propertyName);
    }
}
