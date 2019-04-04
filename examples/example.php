<?php declare(strict_types=1);

namespace HarmonyIO\Orm\Examples;

use Amp\Postgres\ConnectionConfig;
use HarmonyIO\Dbal\Connection;
use HarmonyIO\Orm\Entity\Company;
use HarmonyIO\Orm\Entity\User;
use HarmonyIO\Orm\EntityManager;
use function Amp\Postgres\pool;
use function Amp\Promise\wait;

require_once __DIR__ . '/../vendor/autoload.php';

$postgresPool = pool(
    new ConnectionConfig('127.0.0.1', ConnectionConfig::DEFAULT_PORT, 'username', 'password', 'orm_test')
);

$connection = new Connection($postgresPool);

$entityManager = new EntityManager($connection, $postgresPool);

var_dump(wait($entityManager->find(User::class, 1)));
var_dump(wait($entityManager->find(Company::class, 1)));
