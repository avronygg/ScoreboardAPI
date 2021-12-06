<?php

namespace greek\modules\scoreboard;

use addon\Manager;
use greek\player\Player;

class ScoreboardManager extends Manager
{

    /** @var Scoreboard[] */
    public array $scoreboard = [];

    /**
     * @param Player $player
     */
    public function add(Player $player): void
    {
        $this->scoreboard[$player->getName()] = new Scoreboard($player);
    }

    /**
     * @param Player $player
     */
    public function remove(Player $player): void
    {
        unset($this->scoreboard[$player->getName()]);
    }

    /**
     * @param Player $player
     * @return bool
     */
    public function exist(Player $player): bool
    {
        return isset($this->scoreboard[$player->getName()]);
    }

    /**
     * @param Player $player
     * @return Scoreboard
     */
    public function get(Player $player): Scoreboard
    {
        return $this->scoreboard[$player->getName()];
    }
}