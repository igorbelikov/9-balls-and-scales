<?php

if (isset($_POST['action']) && $_POST['action'] == 'start')
{
    $game->start();
    echo new Response([
        'balls' => $game->ballManager->balls,
        'gameId' => $game->id
    ], ($game->id));
}

if (isset($_POST['action']) && $_POST['action'] == 'replay')
{
    $game->ballManager->reset();
    echo new Response([
        'balls' => $game->ballManager->balls,
    ]);
}

if (isset($_POST['action'], $_POST['index']) && $_POST['action'] == 'mark-as-heavy')
{
    $game->markAsHeavy($_POST['index']);
    echo new Response([
        'balls' => $game->ballManager->balls
    ]);
}