<?php

declare(strict_types=1);

namespace Prison\Permission\EventListener;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use Prison\Permission\Manager\PlayerPermissionManagerInterface;

class PlayerPermissionListener implements Listener
{
    public function __construct(private PlayerPermissionManagerInterface $playerPermissionManager)
    {
    }

    public function onJoin(PlayerJoinEvent $event): void
    {
        $player = $event->getPlayer();

        $this->playerPermissionManager->registerPlayer($player);
    }

    public function onLeave(PlayerQuitEvent $event): void
    {
        $player = $event->getPlayer();

        $this->playerPermissionManager->unregisterPlayer($player);
    }
}
