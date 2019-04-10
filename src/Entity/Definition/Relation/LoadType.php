<?php declare(strict_types=1);

namespace HarmonyIO\Orm\Entity\Definition\Relation;

use HarmonyIO\Orm\Enum;

class LoadType extends Enum
{
    public const LAZY  = 'lazy';
    public const EAGER = 'eager';
}
