<?php

namespace Prison\Economy\EventListener;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use Prison\Economy\Manager\EconomyManagerInterface;

class EconomyListener implements Listener
{
    public function __construct(private EconomyManagerInterface $economyManager)
    {
    }

    public function onJoin(PlayerJoinEvent $event): void
    {
        $player = $event->getPlayer();
        $this->economyManager->createEconomy($player);
    }
}