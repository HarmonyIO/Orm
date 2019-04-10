<?php declare(strict_types=1);

namespace HarmonyIO\Orm\Entity\Definition\Generator;

use HarmonyIO\Orm\Entity\Definition\Definition;

class ArrayCache implements Generator
{
    /** @var Definition[] */
    private $definitions = [];

    public function generate(string $entityClassName): Definition
    {
        if (!isset($this->definitions[$entityClassName])) {
            $this->definitions[$entityClassName] = new Definition($entityClassName);
        }

        return $this->definitions[$entityClassName];
    }
}
