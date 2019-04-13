<?php declare(strict_types=1);

namespace HarmonyIO\Orm\Entity\Definition\Relation;

class ManyToOne extends Relation
{
    /** @var string */
    private $foreignKey;

    /** @var string */
    private $localKey;

    public function __construct(LoadType $loadType, string $entityClassName, string $foreignKey, string $localKey)
    {
        $this->foreignKey = $foreignKey;
        $this->localKey   = $localKey;

        parent::__construct(new RelationType(RelationType::MANY_TO_ONE), $loadType, $entityClassName);
    }

    public function getForeignKey(): string
    {
        return $this->foreignKey;
    }

    public function getLocalKey(): string
    {
        return $this->localKey;
    }
}
