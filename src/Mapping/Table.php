<?php declare(strict_types=1);

namespace HarmonyIO\Orm\Mapping;

class Table
{
    /** @var string */
    private $name;

    /** @var string */
    private $alias;

    public function __construct(string $name, string $alias)
    {
        $this->name  = $name;
        $this->alias = $alias;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getAlias(): string
    {
        return $this->alias;
    }
}
