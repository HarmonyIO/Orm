<?php declare(strict_types=1);

namespace HarmonyIO\Orm\Mapping;

use HarmonyIO\Orm\Enum;

class Type extends Enum
{
    public const FIELD = 'field';
    public const JOIN  = 'join';
}
