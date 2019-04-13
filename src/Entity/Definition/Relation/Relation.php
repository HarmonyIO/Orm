<?php declare(strict_types=1);

namespace HarmonyIO\Orm\Entity\Definition\Relation;

abstract class Relation
{
    /** @var RelationType */
    private $relationType;

    /** @var LoadType */
    private $loadType;

    /** @var string */
    private $entityClassName;

    public function __construct(RelationType $relationType, LoadType $loadType, string $entityClassName)
    {
        $this->relationType    = $relationType;
        $this->loadType        = $loadType;
        $this->entityClassName = $entityClassName;
    }

    public function isRelationType(RelationType $relationType): bool
    {
        return $this->relationType->getValue() === $relationType->getValue();
    }

    public function getLoadType(): LoadType
    {
        return $this->loadType;
    }

    public function getEntityClassName(): string
    {
        return $this->entityClassName;
    }
}
