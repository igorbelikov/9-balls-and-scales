<?php
// 9 balls and scales
//
// тесты:
// - тест на проверку ответа
// - тест на проверку сгенерированных шаров X
// - тест на наличие тяжелого шара X
// - тест на успешный первый шаг
// - тест на успешный результат
// - тест на успешную запись результатов в БД

require_once 'vendor/autoload.php';

use Symfony\Component\Yaml\Yaml;

$phinxConfig = Yaml::parse(file_get_contents('phinx.yml'));
$dbConfig = $phinxConfig['environments'][$phinxConfig['environments']['default_database']];

$db = new \PDO($dbConfig['adapter'] . ':dbname=' . $dbConfig['name'] . ';host=' . $dbConfig['host'], $dbConfig['user'], $dbConfig['pass']);

$log = new Log($db);
$manager = new BallManager();

// ajax handlers
require_once 'handlers.php';