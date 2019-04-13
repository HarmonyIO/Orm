<?php declare(strict_types=1);

namespace HarmonyIO\Orm\Entity\Definition\Relation;

use HarmonyIO\Orm\Enum;

class RelationType extends Enum
{
    public const ONE_TO_ONE   = 'oneToOne';
    public const ONE_TO_MANY  = 'oneToMany';
    public const MANY_TO_ONE  = 'manyToOne';
    public const MANY_TO_MANY = 'manyToMany';
}
