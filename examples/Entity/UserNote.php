<?php declare(strict_types=1);

namespace HarmonyIO\Orm\Examples\Entity;

use Amp\Promise;
use HarmonyIO\Orm\Collection;
use HarmonyIO\Orm\Entity\Entity;

class UserNote extends Entity
{
    /** @var Promise<int> */
    private $id;

    /** @var Promise<string> */
    private $content;

    /** @var Promise<Collection> */
    //private $comments;

    /** @var Promise<User> */
    private $user;

    public function relate(): void
    {
        //$this->oneToMany('comments', UserNoteComment::class);
        $this->manyToOne('user', User::class);
    }

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

    /**
     * @return Promise<Collection>
     */
    public function getComments(): Promise
    {
        return $this->comments;
    }

    public function getUser(): Promise
    {
        return $this->user;
    }
}
