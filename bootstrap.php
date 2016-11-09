<?php
// 9 balls and scales
//
// тесты:
// - тест на проверку ответа
// - тест на проверку сгенерированных шаров
// - тест на наличие тяжелого шара
// - тест на успешный первый шаг
// - тест на успешный результат
// - тест на успешную запись результатов в БД

$db = new PDO('mysql:dbname=test-9balls;host=127.0.0.1', 'root', '');

// models
require_once 'models.php';

// ajax handlers
require_once 'handlers.php';