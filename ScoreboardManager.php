<?php

namespace pluginName\api\scoreboard;

use pocketmine\player\Player;

class ScoreboardManager
{

    /** @var Scoreboard[] */
    public static array $scoreboard = [];

    /**
     * @param Player $player
     * @param string $title
     */
    public static function add(Player $player, string $title): void
    {
        self::$scoreboard[$player->getName()] = new Scoreboard($player, $title);
    }

    /**
     * @param Player $player
     */
    public function remove(Player $player): void
    {
        if (!self::exist($player))
            return;
        
        $scoreboard = self::get($player);
        $scoreboard->removeScoreboard();
        
        unset(self::$scoreboard[$player->getName()]);
    }

    /**
     * @param Player $player
     * @return bool
     */
    public static function exist(Player $player): bool
    {
        return isset(self::$scoreboard[$player->getName()]);
    }

    /**
     * @param Player $player
     * @return Scoreboard|null
     */
    public static function get(Player $player): ?Scoreboard
    {
        if (!self::exist($player)) return null;
        return self::$scoreboard[$player->getName()];
    }
}
