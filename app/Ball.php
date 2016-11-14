<?php

namespace app;

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