<?php

if (isset($_POST['action']) && $_POST['action'] == 'start')
{
    $manager = new BallManager();
    echo new Response([
        'balls' => $manager->balls,
    ]);
}