<?php declare(strict_types=1);

namespace HarmonyIO\Orm\Examples;

use Amp\Loop;
use Amp\Postgres\ConnectionConfig;
use HarmonyIO\Dbal\Connection;
use HarmonyIO\Orm\Collection;
use HarmonyIO\Orm\Entity\Definition\Generator\ArrayCache;
use HarmonyIO\Orm\EntityManager;
use HarmonyIO\Orm\Examples\Entity\Company;
use HarmonyIO\Orm\Examples\Entity\User;
use HarmonyIO\Orm\Examples\Entity\UserNote;
use HarmonyIO\Orm\Hydrator\Hydrator;
use function Amp\Postgres\pool;

require_once __DIR__ . '/../vendor/autoload.php';

$postgresPool = pool(
    new ConnectionConfig('127.0.0.1', ConnectionConfig::DEFAULT_PORT, 'username', 'password', 'orm_test')
);

$connection = new Connection($postgresPool);

$em = new EntityManager($connection, $postgresPool, new ArrayCache(), new Hydrator());

Loop::run(static function () use ($em): \Generator {
    $user = new User();
    $user->setName('Pieter Hordijk');
    $user->setPhoneNumber('1112223334');
    $user->setCompany(yield $em->find(Company::class, 1));

    yield $em->create($user);

    $note = new UserNote();
    $note->setContent('!!!This is my test to see whether it actually creates a note now!!!');
    $note->setUser($user);

    yield $em->create($note);

    /** @var User $user */
    $user = yield $em->refresh($user);

    /** @var Collection $notes */
    $notes = yield $user->getNotes();

    var_dump(count($notes));

    var_dump(yield $em->findAll(UserNote::class));
});
