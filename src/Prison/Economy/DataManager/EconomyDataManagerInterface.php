<?php

declare(strict_types=1);

namespace Prison\Economy\DataManager;

use pocketmine\player\IPlayer;
use Prison\Core\Loader\Interface\LoaderAwareInterface;

interface EconomyDataManagerInterface extends LoaderAwareInterface
{
    public function getMoney(IPlayer $player): array;

    public function saveMoney(IPlayer $player, array $money): void;

    public function createDirectory(): void;
}