<?php

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
        header('Content-Type: application/json');
        $data = [
            'success' => true,
            'data' => $this->data,
        ];
        return json_encode($data);
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
}

/**
 * Class BallManager
 */
class BallManager
{
    /**
     * @var array
     */
    public $balls = [];

    /**
     * BallManager constructor.
     */
    public function __construct()
    {
        $this->balls = $this->generateBalls();
    }

    /**
     * @return Ball[]
     */
    private function generateBalls()
    {
        $balls = array_fill(0, 8, new Ball());
        $balls[] = new Ball(true);
        shuffle($balls);

        return $balls;
    }
}