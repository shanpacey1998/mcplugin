<?php

declare(strict_types=1);

namespace Prison\Rank\EventListener;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use Prison\Rank\DataManager\PlayerRankDataManagerInterface;
use Prison\Rank\Manager\RankManagerInterface;

class PlayerRankListener implements Listener
{
    public function __construct(
        private readonly PlayerRankDataManagerInterface $playerRankDataManager,
        private readonly RankManagerInterface $rankManager,
    ) {
    }

    public function onJoin(PlayerJoinEvent $event): void
    {
        $player = $event->getPlayer();
        $rank = $this->playerRankDataManager->getRank($player);

        if (null !== $rank) {
            return;
        }

        $rank = $this->rankManager->getDefaultRank();

        if (null === $rank) {
            $event->getPlayer()->kick('Error in ranks');

            return;
        }

        $this->playerRankDataManager->saveRank($player, $rank);
    }
}
