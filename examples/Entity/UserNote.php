<?php declare(strict_types=1);

namespace HarmonyIO\Orm\Examples\Entity;

use Amp\Promise;
use Amp\Success;
use HarmonyIO\Orm\Entity\Entity;

class UserNote extends Entity
{
    /** @var Promise<int> */
    private $id;

    /** @var Promise<string> */
    private $content;

    /** @var Promise<User> */
    private $user;

    public function relate(): void
    {
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
     * @return Promise<UserNote>
     */
    public function setContent(string $content): Promise
    {
        $this->content = new Success($content);

        $this->markPropertyAsChanged('content');

        return new Success($this);
    }

    public function getUser(): Promise
    {
        return $this->user;
    }

    /**
     * @return Promise<UserNote>
     */
    public function setUser(User $user): Promise
    {
        $this->user = new Success($user);

        $this->markPropertyAsChanged('user');

        return new Success($this);
    }
}
