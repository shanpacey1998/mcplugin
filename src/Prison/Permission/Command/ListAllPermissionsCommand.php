<?php

namespace Prison\Permission\Command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\permission\PermissionManager;
use pocketmine\player\Player;
use Prison\Core\Loader\Interface\LoaderAwareInterface;
use Prison\Core\Loader\Loader;
use Prison\Core\Loader\Trait\LoaderAwareTrait;
use Prison\Core\Logger\Trait\LoggerTrait;
use Prison\Core\Validator\CommandValidator;
use Prison\Core\Validator\Constraints\Validator\Validator;
use Prison\Permission\PermissionList;
use Prison\Permission\Validator\Constraints\ListPermissionsConstraint;

class ListAllPermissionsCommand extends Command
{
    use LoggerTrait;

    public function __construct()
    {
        parent::__construct(
            'listallpermissions',
            'List all registered permissions',
            '/listallpermissions',
            ['listallperms']
        );

        $this->setPermission(PermissionList::LIST_PERMISSIONS_COMMAND);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        $permissionManager = PermissionManager::getInstance();

        foreach ($permissionManager->getPermissions() as $permission) {
            $this->sendInfo($sender, $permission->getName());
        }
    }
}