<?php

declare(strict_types=1);

namespace Prison\Permission\Command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\permission\PermissionManager;
use Prison\Core\Logger\Trait\LoggerTrait;
use Prison\Permission\PermissionList;

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
