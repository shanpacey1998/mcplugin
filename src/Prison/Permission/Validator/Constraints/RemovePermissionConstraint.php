<?php

declare(strict_types=1);

namespace Prison\Permission\Validator\Constraints;

use pocketmine\permission\PermissionManager;
use pocketmine\player\Player;
use Prison\Core\Loader\Interface\LoaderAwareInterface;
use Prison\Core\Loader\Loader;
use Prison\Core\Loader\Trait\LoaderAwareTrait;
use Prison\Core\Validator\Constraints\ConstraintInterface;

class RemovePermissionConstraint implements ConstraintInterface, LoaderAwareInterface
{
    use LoaderAwareTrait;

    public function __construct(Loader $loader)
    {
        $this->setLoader($loader);
    }

    public function validate(mixed $value): ?string
    {
        if (!is_array($value)) {
            return 'Invalid arguments passed';
        }

        if (2 !== count($value)) {
            return 'Invalid number of arguments passed';
        }

        [$playerName, $permissionName] = $value;

        $player = $this->loader->getServer()->getPlayerExact($playerName);

        if (!$player instanceof Player) {
            return sprintf('Could not find the player %s', $playerName);
        }

        $permissionManager = PermissionManager::getInstance();

        $permission = $permissionManager->getPermission($permissionName);

        if (null === $permission) {
            return sprintf('The permission %s does not exist', $permissionName);
        }

        if (!$player->hasPermission($permission->getName())) {
            return sprintf('%s does not have the permission %s', $player->getName(), $permission->getName());
        }

        return null;
    }
}