<?php declare(strict_types=1);

namespace HarmonyIO\Orm\Entity\Definition\Relation;

class ManyToMany extends Relation
{
    /** @var string */
    private $linkTableName;

    /** @var string */
    private $foreignKey;

    /** @var string */
    private $foreignLink;

    /** @var string */
    private $localKey;

    /** @var string */
    private $localLink;

    public function __construct(
        LoadType $loadType,
        string $entityClassName,
        string $linkTableName,
        string $foreignKey,
        string $foreignLink,
        string $localKey,
        string $localLink
    ) {
        $this->linkTableName = $linkTableName;
        $this->foreignKey    = $foreignKey;
        $this->foreignLink   = $foreignLink;
        $this->localKey      = $localKey;
        $this->localLink     = $localLink;

        parent::__construct(new RelationType(RelationType::MANY_TO_MANY), $loadType, $entityClassName);
    }

    public function getLinkTableName(): string
    {
        return $this->linkTableName;
    }

    public function getForeignKey(): string
    {
        return $this->foreignKey;
    }

    public function getForeignLink(): string
    {
        return $this->foreignLink;
    }

    public function getLocalKey(): string
    {
        return $this->localKey;
    }

    public function getLocalLink(): string
    {
        return $this->localLink;
    }
}
