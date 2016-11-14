<?php

namespace app;

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