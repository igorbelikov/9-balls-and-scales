<?php

/**
 * Class BallsTest
 */
class BallsTest extends \PHPUnit\Framework\TestCase
{
    public function testGenerateBalls()
    {
        $manager = new BallManager();
        $this->assertEquals(9, count($manager->balls));
        $this->assertEquals(9, count($manager->generate()));
        $this->assertEquals(9, count($manager->reset()->balls));
    }

    public function testClearHeavy()
    {
        $manager = new BallManager();
        $manager->clearHeavy();
        $this->assertEquals(null, $manager->getHeavy());
    }

    public function testChangeHeavy()
    {
        $manager = new BallManager();
        $this->assertEquals(new Ball(true), $manager->getHeavy());
        $manager->clearHeavy();
        $this->assertEquals(null, $manager->getHeavy());
        $manager->markAsHeavy(3);
        $this->assertEquals(true, $manager->balls[3]->isHeavy);
    }

    public function testHeavyBallExists()
    {
        $manager = new BallManager();
        $this->assertEquals(new Ball(true), $manager->getHeavy());
    }

    public function testGenerateResponse()
    {
        $response = new Response();
        $data = $response->generateData();
        $this->assertArrayHasKey('success', $data);
        $this->assertArrayHasKey('data', $data);
    }

    public function testScales()
    {
        $scales = new Scales();
        $this->assertEquals([new Ball(true)], $scales->weigh(
            [new Ball(true)],
            [new Ball(false)]
        ));
        $heavyBalls = [new Ball(true), new Ball(false)];
        $this->assertEquals($heavyBalls, $scales->weigh(
            $heavyBalls,
            [new Ball(false), new Ball(false)]
        ));
        $this->assertEquals([], $scales->weigh(
            [new Ball(false)],
            [new Ball(false)]
        ));
    }
}