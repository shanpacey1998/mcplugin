<?php

namespace Prison\Permission\Manager;

use pocketmine\player\Player;
use Prison\Core\Loader\Loader;
use Prison\Core\Loader\Trait\LoaderAwareTrait;
use Prison\Core\Logger\Trait\LoggerTrait;
use Prison\Permission\DataManager\PlayerPermissionDataManagerInterface;

class PlayerPermissionManager implements PlayerPermissionManagerInterface
{
    use LoaderAwareTrait;
    use LoggerTrait;

    public function __construct(
        private PlayerPermissionDataManagerInterface $playerPermissionDataManager,
        Loader $loader,
    ) {
        $this->setLoader($loader);
    }

    public function addPermission(Player $player, string $permission): void
    {
        $permissions = $this->playerPermissionDataManager->getPermissions($player);

        if (in_array($permission, $permissions)) {
            $this->logInfo(sprintf('Player %s already has the permission %s', $player->getName(), $permission));

            return;
        }

        $permissions[] = $permission;

        $this->logInfo(sprintf('Adding permission %s to %s', $permission, $player->getName()));

        $this->playerPermissionDataManager->savePermissions($player, $permissions);
        $this->resyncPermissions($player);
    }

    public function removePermission(Player $player, string $permission): void
    {
        $permissions = $this->playerPermissionDataManager->getPermissions($player);

        if (!in_array($permission, $permissions)) {
            $this->logInfo(sprintf('Player %s has no permission %s', $player->getName(), $permission));

            return;
        }

        foreach ($permissions as $index => $existingPermission) {
            if ($permission === $existingPermission) {
                $this->logInfo(sprintf('Removed permission %s from %s', $permission, $player->getName()));

                unset($permissions[$index]);
            }
        }

        $this->playerPermissionDataManager->savePermissions($player, $permissions);
        $this->resyncPermissions($player);
    }

    public function resyncPermissions(Player $player): void
    {
        $permissions = $this->playerPermissionDataManager->getPermissions($player);

        $pocketminePermissions = [];

        foreach ($permissions as $permission) {
            $pocketminePermissions[$permission] = true;
        }

        $attachment = $this->loader->getAttachment($this->getValidUUID($player));

        if (null === $attachment) {
            $this->logWarning(sprintf('Player %s has no attachment', $player->getName()));

            return;
        }

        $attachment->clearPermissions();
        $attachment->setPermissions($pocketminePermissions);

        $this->logInfo(sprintf('Updated Permissions for %s', $player->getName()));

        foreach ($player->getEffectivePermissions() as $effectivePermission) {
            $this->logInfo($effectivePermission->getPermission());
        }
    }

    public function unregisterPlayer(Player $player): void
    {
        $uniqueId = $this->getValidUUID($player);

        $attachment = $this->loader->getAttachment($uniqueId);

        if(null !== $attachment) {
            $player->removeAttachment($attachment);
            $this->loader->removeAttachment($uniqueId);
        }
    }

    public function unregisterPlayers(): void
    {
        foreach($this->loader->getServer()->getOnlinePlayers() as $player)
        {
            $this->unregisterPlayer($player);
        }
    }

    public function registerPlayer(Player $player): void
    {
        $uniqueId = $this->getValidUUID($player);

        if ($this->loader->hasAttachment($uniqueId))
        {
            $this->logWarning(sprintf('Player %s has already registered', $player->getName()));

            return;
        }

        $attachment = $player->addAttachment($this->loader);
        $this->loader->setAttachment($uniqueId, $attachment);
        $this->resyncPermissions($player);
    }

    public function registerPlayers(): void
    {
        foreach($this->loader->getServer()->getOnlinePlayers() as $player)
        {
            $this->registerPlayer($player);
        }
    }

    private function getValidUUID(Player $player): string
    {
        return $player->getUniqueId()->toString();
    }
}