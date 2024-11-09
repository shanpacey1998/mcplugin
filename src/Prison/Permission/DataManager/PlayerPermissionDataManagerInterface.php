<?php

declare(strict_types=1);

namespace Prison\Permission\DataManager;

use pocketmine\player\IPlayer;
use Prison\Core\Loader\Interface\LoaderAwareInterface;

interface PlayerPermissionDataManagerInterface extends LoaderAwareInterface
{
    /**
     * @param string[] $permissions
     * @return void
     */
    public function savePermissions(IPlayer $player, array $permissions): void;

    /**
     * @return string[]
     */
    public function getPermissions(IPlayer $player): array;

    public function createDirectory(): void;
}