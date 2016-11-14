<?php

namespace app;

/**
 * Class Game
 */
class Game
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var BallManager
     */
    public $ballManager;

    /**
     * @var GameLog
     */
    public $log;

    /**
     * Game constructor.
     * @param BallManager $ballManager
     * @param GameLog $log
     */
    public function __construct($ballManager, $log)
    {
        $this->ballManager = $ballManager;
        $this->log = $log;
    }

    /**
     * @param bool $replay
     * @return bool
     */
    public function start($replay = false)
    {
        $id = $this->log->createGame();
        if ($id) {
            $this->id = $id;
            $_SESSION['gameId'] = $this->id;
            return $this->log->createAction($replay ? GameLog::ACTION_REPLAY : GameLog::ACTION_START, $this->getId());
        }
        return false;
    }

    /**
     * @param $index
     * @return bool
     */
    public function markAsHeavy($index)
    {
        if ($this->ballManager->markAsHeavy($index)) {
            return $this->log->createAction(GameLog::ACTION_CHANGE_HEAVY_BALL, $this->getId());
        }
        return false;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return isset($_SESSION['gameId']) ? $_SESSION['gameId'] : $this->id;
    }
}