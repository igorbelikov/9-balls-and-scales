<?php

use app\GameLog;
use app\Response;
use app\Scales;

if (isset($_POST['action'], $_POST['replay']) && $_POST['action'] == 'start')
{
    $game->start();
    echo new Response([
        'balls' => $game->ballManager->balls,
        'gameId' => $game->id,
        'actionLabel' => GameLog::getLabels($_POST['replay'] ? GameLog::ACTION_REPLAY : GameLog::ACTION_START)
    ], ($game->id));
}

if (isset($_POST['action']) && $_POST['action'] == 'replay')
{
    $game->ballManager->reset();
    echo new Response([
        'balls' => $game->ballManager->balls,
    ]);
}

if (isset($_POST['action'], $_POST['balls1'], $_POST['balls2']) && $_POST['action'] == 'weigh')
{
    $scales = new Scales();
    echo new Response([
        'balls' => $scales->weigh(json_decode($_POST['balls1']), json_decode($_POST['balls2']))
    ]);
}

if (isset($_POST['action'], $_POST['index']) && $_POST['action'] == 'mark-as-heavy')
{
    echo new Response([
        'balls' => $game->ballManager->balls,
        'actionLabel' => GameLog::getLabels(GameLog::ACTION_CHANGE_HEAVY_BALL)
    ], $game->markAsHeavy($_POST['index']));
}