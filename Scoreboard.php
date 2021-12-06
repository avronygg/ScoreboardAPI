<?php

declare(strict_types=1);

namespace greek\modules\scoreboard;

use greek\Loader;
use greek\messages\MessageContainer;
use greek\player\Player;
use pocketmine\network\mcpe\protocol\RemoveObjectivePacket;
use pocketmine\network\mcpe\protocol\SetDisplayObjectivePacket;
use pocketmine\network\mcpe\protocol\SetScorePacket;
use pocketmine\network\mcpe\protocol\types\ScorePacketEntry;


class Scoreboard
{

    /** @var int */
    private const SORT_ASCENDING = 0;
    /** @var string */
    private const SLOT_SIDEBAR = 'sidebar';

    /** @var Player */
    private Player $player;
    /** @var string */
    private string $title;
    /** @var ScorePacketEntry[] */
    private array $lines = [];

    /**
     * Scoreboard constructor.
     * @param Player $player
     */
    public function __construct(Player $player)
    {
        $this->player = $player;
        $this->title = (new MessageContainer('scoreborad-title'))->__toString();
        $this->initScoreboard();
    }

    private function initScoreboard(): void
    {
        $pkt = new SetDisplayObjectivePacket();
        $pkt->objectiveName = $this->player->getName();
        $pkt->displayName = $this->title;
        $pkt->sortOrder = self::SORT_ASCENDING;
        $pkt->displaySlot = self::SLOT_SIDEBAR;
        $pkt->criteriaName = 'dummy';
        $this->player->getNetworkSession()->sendDataPacket($pkt);
    }

    public function clearScoreboard(): void
    {
        $pkt = new SetScorePacket();
        $pkt->entries = $this->lines;
        $pkt->type = SetScorePacket::TYPE_REMOVE;
        $this->player->getNetworkSession()->sendDataPacket($pkt);
        $this->lines = [];
    }

    /**
     * @param int $id
     * @param string $line
     */
    public function addLine(int $id, string $line): void
    {
        $entry = new ScorePacketEntry();
        $entry->type = ScorePacketEntry::TYPE_FAKE_PLAYER;

        if (isset($this->lines[$id])) {
            $pkt = new SetScorePacket();
            $pkt->entries[] = $this->lines[$id];
            $pkt->type = SetScorePacket::TYPE_REMOVE;
            $this->player->getNetworkSession()->sendDataPacket($pkt);
            unset($this->lines[$id]);
        }
        $entry->score = $id;
        $entry->scoreboardId = $id;
        $entry->actorUniqueId = $this->player->getId();
        $entry->objectiveName = $this->player->getName();
        $entry->customName = $line;
        $this->lines[$id] = $entry;

        $pkt = new SetScorePacket();
        $pkt->entries[] = $entry;
        $pkt->type = SetScorePacket::TYPE_CHANGE;
        $this->player->getNetworkSession()->sendDataPacket($pkt);
    }

    /**
     * @param int $id
     */
    public function removeLine(int $id): void
    {
        if (isset($this->lines[$id])) {
            $line = $this->lines[$id];
            $packet = new SetScorePacket();
            $packet->entries[] = $line;
            $packet->type = SetScorePacket::TYPE_REMOVE;
            $this->player->getNetworkSession()->sendDataPacket($packet);
            unset($this->lines[$id]);
        }
    }

    public function removeScoreboard(): void
    {
        $packet = new RemoveObjectivePacket();
        $packet->objectiveName = $this->player->getName();
        $this->player->getNetworkSession()->sendDataPacket($packet);
    }
}