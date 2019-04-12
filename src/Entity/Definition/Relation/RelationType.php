<?php declare(strict_types=1);

namespace HarmonyIO\Orm\Entity\Definition\Relation;

use HarmonyIO\Orm\Enum;

class RelationType extends Enum
{
    public const HAS_ONE  = 'hasOne';
    public const HAS_MANY = 'hasMany';
}
