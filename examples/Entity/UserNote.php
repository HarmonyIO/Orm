<?php declare(strict_types=1);

namespace HarmonyIO\Orm\Examples\Entity;

use HarmonyIO\Orm\Collection;
use HarmonyIO\Orm\Entity\Entity;

class UserNote extends Entity
{
    /** @var int */
    private $id;

    /** @var string */
    private $content;

    /** @var Collection */
    private $comments;

    public function relate(): void
    {
        $this->oneToMany('comments', UserNoteComment::class);
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getComments(): Collection
    {
        return $this->comments;
    }
}
