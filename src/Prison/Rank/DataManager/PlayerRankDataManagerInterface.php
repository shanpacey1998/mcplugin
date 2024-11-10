<?php

declare(strict_types=1);

namespace Prison\Rank\DataManager;

use pocketmine\player\Player;
use Prison\Core\DataManager\DataManagerInterface;
use Prison\Core\Loader\Interface\LoaderAwareInterface;
use Prison\Rank\Dto\Rank;

interface PlayerRankDataManagerInterface extends LoaderAwareInterface, DataManagerInterface
{
    public function getRank(Player $player): ?Rank;

    public function saveRank(Player $player, Rank $rank): void;
}