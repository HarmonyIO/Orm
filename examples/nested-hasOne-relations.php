<?php declare(strict_types=1);

namespace HarmonyIO\Orm\Examples;

use Amp\Postgres\ConnectionConfig;
use HarmonyIO\Dbal\Connection;
use HarmonyIO\Orm\Entity\Definition\Generator\ArrayCache;
use HarmonyIO\Orm\EntityManager;
use HarmonyIO\Orm\Examples\Entity\User;
use function Amp\Postgres\pool;
use function Amp\Promise\wait;

require_once __DIR__ . '/../vendor/autoload.php';

$postgresPool = pool(
    new ConnectionConfig('127.0.0.1', ConnectionConfig::DEFAULT_PORT, 'username', 'password', 'orm_test')
);

$connection = new Connection($postgresPool);

$em = new EntityManager($connection, $postgresPool, new ArrayCache());

var_dump(wait($em->find(User::class, 1)));
