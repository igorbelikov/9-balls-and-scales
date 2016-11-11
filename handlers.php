<?php

if (isset($_POST['action']) && $_POST['action'] == 'start')
{
    echo new Response([
        'balls' => $manager->balls,
        'log' => $log->createGame()
    ]);
}

if (isset($_POST['action']) && $_POST['action'] == 'replay')
{
    $manager->reset();
    echo new Response([
        'balls' => $manager->balls,
    ]);
}

if (isset($_POST['action'], $_POST['index']) && $_POST['action'] == 'mark-as-heavy')
{
    $manager->markAsHeavy($_POST['index']);
    echo new Response([
        'balls' => $manager->balls
    ]);
}