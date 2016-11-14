<?php

namespace app;

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
    protected function prepare($balls1, $balls2)
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
     * @param array $b
     * @return array|bool
     */
    public function weigh(array $a, array $b)
    {
        return $this->prepare($a, $b);
    }
}