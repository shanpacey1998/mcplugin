<?php

namespace Prison\Economy\Manager;

use pocketmine\player\IPlayer;
use pocketmine\player\Player;
use Prison\Core\Loader\Loader;
use Prison\Core\Loader\Trait\LoaderAwareTrait;
use Prison\Core\Logger\Trait\LoggerTrait;
use Prison\Economy\DataManager\EconomyDataManagerInterface;

class EconomyManager implements EconomyManagerInterface
{
    use LoaderAwareTrait;
    use LoggerTrait;

    public function __construct(Loader $loader, private readonly EconomyDataManagerInterface $economyDataManager)
    {
        $this->setLoader($loader);
    }

    public function addMoney(string $playerName, int $amount): void
    {
        $player = $this->loader->getServer()->getOfflinePlayer($playerName);
        $moneyData = $this->economyDataManager->getMoney($player);
        $balance = 0;

        if (!$moneyData['balance'] == 0) {
            $balance = $moneyData['balance'];
        }

        $moneyData['balance'] = $balance + $amount;

        $this->logInfo(sprintf('Adding %d money to %s', $amount, $player->getName()));

        $this->economyDataManager->saveMoney($player, $moneyData);
    }

    public function subtractMoney(string $playerName, int $amount): void
    {
        $player = $this->loader->getServer()->getOfflinePlayer($playerName);
        $moneyData = $this->economyDataManager->getMoney($player);
        $balance = 0;

        if (!$moneyData['balance'] == 0) {
            $balance = $moneyData['balance'];
        }

        if ($balance === 0) {
            $this->logInfo(sprintf('Balance for %s is already 0, cannot sub more money!',$player->getName()));
            return;
        }

        $moneyData['balance'] = $balance - $amount;

        $this->logInfo(sprintf('Subtracting %d money from %s', $amount, $player->getName()));

        $this->economyDataManager->saveMoney($player, $moneyData);
    }

    public function setMoney(string $playerName, int $amount): void
    {
        $player = $this->loader->getServer()->getOfflinePlayer($playerName);
        $moneyData = $this->economyDataManager->getMoney($player);

        $moneyData['balance'] = $amount;

        $this->logInfo(sprintf('Resetting balance to 0 for %s', $player->getName()));

        $this->economyDataManager->saveMoney($player, $moneyData);
    }

    public function hasMoney(string $playerName, int $amount): bool
    {
        $player = $this->loader->getServer()->getOfflinePlayer($playerName);
        $moneyData = $this->economyDataManager->getMoney($player);

        if ($amount < $moneyData['balance']) {
            return true;
        }

        return false;
    }

    public function getMoney(string $playerName): int
    {
        $player = $this->loader->getServer()->getOfflinePlayer($playerName);
        $moneyData = $this->economyDataManager->getMoney($player);

        return $moneyData['balance'];
    }

    public function createEconomy(IPlayer $player): void
    {
        $moneyData = $this->economyDataManager->getMoney($player);
        $balance = 0;

        if ($moneyData['balance'] > 0) {
            $balance = $moneyData['balance'];
        }

        $this->economyDataManager->saveMoney($player, ['balance' => $balance]);
    }

    public function setPlayerMoney(IPlayer $player, int $amount): void
    {
        $this->setMoney($player->getName(), $amount);
    }

    public function addPlayerMoney(IPlayer $player, int $amount): void
    {
        $this->addMoney($player->getName(), $amount);
    }

    public function subtractPlayerMoney(IPlayer $player, int $amount): void
    {
        $this->subtractMoney($player->getName(), $amount);
    }

    public function hasPlayerMoney(IPlayer $player, int $amount): bool
    {
        return $this->hasMoney($player->getName(), $amount);
    }

    public function getPlayerMoney(IPlayer $player): int
    {
        return $this->getMoney($player->getName());
    }
}