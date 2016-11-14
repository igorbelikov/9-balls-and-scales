<?php

namespace app;

use PDO;

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