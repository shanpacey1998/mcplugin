<?php

declare(strict_types=1);

namespace Prison\Economy\Manager;

use Ifera\ScoreHud\event\PlayerTagUpdateEvent;
use Ifera\ScoreHud\scoreboard\ScoreTag;
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

        if (!$player instanceof Player) {
            return;
        }

        $moneyData = $this->economyDataManager->getMoney($player);
        $balance = 0;

        if (!$moneyData['balance'] == 0) {
            $balance = $moneyData['balance'];
        }

        $moneyData['balance'] = $balance + $amount;

        $this->logInfo(sprintf('Adding %d money to %s', $amount, $player->getName()));

        $this->economyDataManager->saveMoney($player, $moneyData);
        $event = new PlayerTagUpdateEvent($player, new ScoreTag("economy.money", (string) $moneyData['balance']));
        $event->call();
    }

    public function subtractMoney(string $playerName, int $amount): void
    {
        $player = $this->loader->getServer()->getOfflinePlayer($playerName);

        if (!$player instanceof Player) {
            return;
        }

        $moneyData = $this->economyDataManager->getMoney($player);
        $balance = 0;

        if (!$moneyData['balance'] == 0) {
            $balance = $moneyData['balance'];
        }

        $moneyData['balance'] = $balance - $amount;

        $this->logInfo(sprintf('Subtracting %d money from %s', $amount, $player->getName()));

        $this->economyDataManager->saveMoney($player, $moneyData);
        $event = new PlayerTagUpdateEvent($player, new ScoreTag("economy.money", (string) $moneyData['balance']));
        $event->call();
    }

    public function setMoney(string $playerName, int $amount): void
    {
        $player = $this->loader->getServer()->getOfflinePlayer($playerName);

        if (!$player instanceof Player) {
            return;
        }

        $moneyData = $this->economyDataManager->getMoney($player);

        $moneyData['balance'] = $amount;

        $this->logInfo(sprintf('Setting balance to %d for %s', $amount, $player->getName()));

        $this->economyDataManager->saveMoney($player, $moneyData);
        $event = new PlayerTagUpdateEvent($player, new ScoreTag("economy.money", (string) $moneyData['balance']));
        $event->call();
    }

    public function hasMoney(string $playerName, int $amount): bool
    {
        $player = $this->loader->getServer()->getOfflinePlayer($playerName);

        if (!$player instanceof Player) {
            return false;
        }

        $moneyData = $this->economyDataManager->getMoney($player);

        return $amount < $moneyData['balance'];
    }

    public function getMoney(string $playerName): int
    {
        $player = $this->loader->getServer()->getOfflinePlayer($playerName);

        if (!$player instanceof Player) {
            return 0;
        }

        $moneyData = $this->economyDataManager->getMoney($player);

        return $moneyData['balance'];
    }

    public function createEconomy(IPlayer $player): void
    {
        $moneyData = $this->economyDataManager->getMoney($player);
        $balance = 0;
        $player = $this->loader->getServer()->getOfflinePlayer($player->getName());

        if ($moneyData['balance'] > 0) {
            $balance = $moneyData['balance'];
        }

        $this->economyDataManager->saveMoney($player, ['balance' => $balance]);
        $event = new PlayerTagUpdateEvent($player, new ScoreTag("economy.money", (string) $moneyData['balance']));
        $event->call();
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
