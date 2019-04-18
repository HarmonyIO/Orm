<?php declare(strict_types=1);

namespace HarmonyIO\Orm\Examples\Entity;

use Amp\Promise;
use HarmonyIO\Orm\Entity\Entity;

class UserNoteComment extends Entity
{
    /** @var Promise<int> */
    private $id;

    /** @var Promise<string> */
    private $content;

    /**
     * @return Promise<int>
     */
    public function getId(): Promise
    {
        return $this->id;
    }

    /**
     * @return Promise<string>
     */
    public function getContent(): Promise
    {
        return $this->content;
    }
}
