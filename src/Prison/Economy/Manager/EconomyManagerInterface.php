<?php

namespace Prison\Economy\Manager;

use pocketmine\player\IPlayer;
use Prison\Core\Loader\Interface\LoaderAwareInterface;

interface EconomyManagerInterface extends LoaderAwareInterface
{
    public function setPlayerMoney(IPlayer $player, int $amount): void;
    public function setMoney(string $playerName, int $amount): void;

    public function addPlayerMoney(IPlayer $player, int $amount): void;

    public function addMoney(string $playerName, int $amount): void;

    public function subtractPlayerMoney(IPlayer $player, int $amount): void;

    public function subtractMoney(string $playerName, int $amount): void;

    public function hasPlayerMoney(IPlayer $player, int $amount): bool;

    public function hasMoney(string $playerName, int $amount): bool;

    public function getPlayerMoney(IPlayer $player): int;

    public function getMoney(string $playerName): int;

    public function createEconomy(IPlayer $player): void;
}