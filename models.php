<?php

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

/**
 * Class Scales
 */
class Scales
{
    /**
     * @param Ball[] $balls1
     * @param Ball[] $balls2
     * @return Ball[]
     */
    protected function prepareWeight($balls1, $balls2)
    {
        foreach ($balls1 as $ball) {
            if ($ball->isHeavy) {
                return $balls1;
            }
        }

        foreach ($balls2 as $ball) {
            if ($ball->isHeavy) {
                return $balls2;
            }
        }

        return [];
    }

    /**
     * @param array $a
     * @param array $a
     * @return array|bool
     */
    public function weigh(array $a, array $b)
    {
        return $this->prepareWeight($a, $b);
    }
}

/**
 * Class Log
 */
class GameLog
{
    const ACTION_START = 1;
    const ACTION_REPLAY = 2;
    const ACTION_NEXT_STEP = 3;
    const ACTION_CHANGE_HEAVY_BALL = 4;
    const ACTION_END = 5;

    /**
     * @var PDO
     */
    public $db;

    /**
     * Log constructor.
     * @param $db
     */
    public function __construct($db)
    {
        $this->db = $db;
    }

    /**
     * @param null|int $created
     * @return bool|string
     */
    public function createGame($created = null)
    {
        if ($created === null) {
            $created = time();
        }
        $stmt = $this->db->prepare('INSERT INTO game(created) VALUES(:created)');
        $stmt->bindParam(':created', $created, PDO::PARAM_INT);
        if ($stmt->execute()) {
            return $this->db->lastInsertId();
        }
        return false;
    }

    /**
     * @param int $action
     * @param int $gameId
     * @return bool
     */
    public function createAction($action, $gameId)
    {
        $stmt = $this->db->prepare('INSERT INTO action(game_id, action, created) VALUES(:gameId, :action, :created)');
        $stmt->bindParam(':gameId', $gameId, PDO::PARAM_INT);
        $stmt->bindParam(':action', $action, PDO::PARAM_INT);
        $stmt->bindParam(':created', time(), PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * @param null $key
     * @return array|string|null
     */
    public static function getLabels($key = null)
    {
        $labels = [
            self::ACTION_START => 'Start game.',
            self::ACTION_REPLAY => 'Replay game.',
            self::ACTION_NEXT_STEP => 'Go to next step.',
            self::ACTION_CHANGE_HEAVY_BALL => 'Change heavy ball.',
            self::ACTION_END => 'End game.',
        ];

        if ($key === null) {
            return $labels;
        }

        return isset($labels[$key]) ? $labels[$key] : null;
    }
}

/**
 * Class Response
 */
class Response
{
    /**
     * @var bool
     */
    public $success = true;

    /**
     * @var array
     */
    public $data = [];

    /**
     * Response constructor.
     * @param array $data
     * @param bool $success
     */
    public function __construct($data = [], $success = true)
    {
        $this->data = $data;
        $this->success = $success;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->prepareResponse();
    }

    /**
     * @return array
     */
    public function generateData()
    {
        return [
            'success' => true,
            'data' => $this->data,
        ];
    }

    /**
     * @return string
     */
    protected function prepareResponse()
    {
        header('Content-Type: application/json');
        return json_encode($this->generateData());
    }
}

/**
 * Class Ball
 */
class Ball
{
    /**
     * @var bool
     */
    public $isHeavy = false;

    /**
     * Ball constructor.
     * @param bool $isHeavy
     */
    public function __construct($isHeavy = false)
    {
        $this->isHeavy = $isHeavy;
    }

    /**
     * @param bool $isHeavy
     * @return $this
     */
    public function setIsHeavy($isHeavy = false)
    {
        $this->isHeavy = $isHeavy;
        return $this;
    }
}

/**
 * Class BallManager
 */
class BallManager
{
    /**
     * @var Ball[]
     */
    public $balls = [];

    /**
     * BallManager constructor.
     */
    public function __construct()
    {
        $this->reset();
    }

    /**
     * @return $this
     */
    public function reset()
    {
        $this->balls = $this->generate();
        return $this;
    }

    /**
     * @return Ball[]
     */
    public function generate()
    {
        for ($i = 0; $i < 8; $i++) {
            $balls[] = new Ball();
        }
        $balls[] = new Ball(true);
        shuffle($balls);

        return $balls;
    }

    /**
     * @return Ball|null
     */
    public function getHeavy()
    {
        foreach ($this->balls as $index => &$ball) {
            if ($ball->isHeavy) {
                return $ball;
            }
        }
        return null;
    }

    /**
     * @return bool
     */
    public function clearHeavy()
    {
        $heavyBall = $this->getHeavy();
        if ($heavyBall) {
            $heavyBall->setIsHeavy(false);
            return true;
        }
        return false;
    }

    /**
     * @param int $index
     * @return bool
     */
    public function markAsHeavy($index)
    {
        $this->clearHeavy();
        if (isset($this->balls[$index])) {
            $this->balls[$index]->setIsHeavy(true);
            return $index;
        }
        return false;
    }
}