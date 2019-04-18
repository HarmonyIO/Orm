<?php declare(strict_types=1);

namespace HarmonyIO\Orm\Examples;

use Amp\Loop;
use Amp\Postgres\ConnectionConfig;
use HarmonyIO\Dbal\Connection;
use HarmonyIO\Orm\Entity\Definition\Generator\ArrayCache;
use HarmonyIO\Orm\EntityManager;
use HarmonyIO\Orm\Examples\Entity\User;
use HarmonyIO\Orm\Hydrator\Hydrator;
use function Amp\Postgres\pool;

require_once __DIR__ . '/../vendor/autoload.php';

$postgresPool = pool(
    new ConnectionConfig('127.0.0.1', ConnectionConfig::DEFAULT_PORT, 'username', 'password', 'orm_test')
);

$connection = new Connection($postgresPool);

$em = new EntityManager($connection, $postgresPool, new ArrayCache(), new Hydrator());

Loop::run(static function () use ($em): \Generator {
    /** @var User */
    $user = yield $em->find(User::class, 3);

    var_dump(yield $em->delete($user));
});
