<?php declare(strict_types=1);

namespace HarmonyIO\Orm\Entity\Definition\Relation;

class Relation
{
    /** @var RelationType */
    private $relationType;

    /** @var LoadType */
    private $loadType;

    /** @var string */
    private $entityClassName;

    /** @var string */
    private $foreignKey;

    /** @var string|null */
    private $localKey;

    public function __construct(
        RelationType $relationType,
        LoadType $loadType,
        string $entityClassName,
        string $foreignKey,
        ?string $localKey = null
    ) {
        $this->relationType    = $relationType;
        $this->loadType        = $loadType;
        $this->entityClassName = $entityClassName;
        $this->foreignKey      = $foreignKey;
        $this->localKey        = $localKey;
    }

    public function getRelationType(): RelationType
    {
        return $this->relationType;
    }

    public function getLoadType(): LoadType
    {
        return $this->loadType;
    }

    public function getEntityClassName(): string
    {
        return $this->entityClassName;
    }

    public function getForeignKey(): string
    {
        return $this->foreignKey;
    }

    public function getLocalKey(): ?string
    {
        return $this->localKey;
    }
}
