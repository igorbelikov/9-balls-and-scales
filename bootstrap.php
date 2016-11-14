<?php
// 9 balls and scales

session_start();

require_once 'vendor/autoload.php';

use Symfony\Component\Yaml\Yaml;

$phinxConfig = Yaml::parse(file_get_contents('phinx.yml'));
$dbConfig = $phinxConfig['environments'][$phinxConfig['environments']['default_database']];

$db = new \PDO($dbConfig['adapter'] . ':dbname=' . $dbConfig['name'] . ';host=' . $dbConfig['host'], $dbConfig['user'], $dbConfig['pass']);

$game = new \app\Game(new \app\BallManager(), new \app\GameLog($db));

// ajax handlers
require_once 'handlers.php';