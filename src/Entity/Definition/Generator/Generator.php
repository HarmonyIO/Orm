<?php declare(strict_types=1);

namespace HarmonyIO\Orm\Entity\Definition\Generator;

use HarmonyIO\Orm\Entity\Definition\Definition;

interface Generator
{
    public function generate(string $entityClassName): Definition;
}
