<?php

/**
 * Class Log
 */
class Log
{
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
     * @return bool
     */
    public function createGame($created = null)
    {
        if ($created === null) {
            $created = time();
        }
        $stmt = $this->db->prepare('INSERT INTO game(created) VALUES(:created)');
        $stmt->bindParam(':created', $created, PDO::PARAM_INT);
        return $stmt->execute();
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
    public function __toString()
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