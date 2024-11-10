<?php

declare(strict_types=1);

namespace Prison\Permission\Manager;

use pocketmine\player\Player;
use Prison\Core\Loader\Interface\LoaderAwareInterface;

interface PlayerPermissionManagerInterface extends LoaderAwareInterface
{
    public function addPermission(Player $player, string $permission): void;

    public function removePermission(Player $player, string $permission): void;

    public function resyncPermissions(Player $player): void;

    public function unregisterPlayer(Player $player): void;

    public function unregisterPlayers(): void;

    public function registerPlayer(Player $player): void;

    public function registerPlayers(): void;
}
