<?php

namespace Prison\Permission\Command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use Prison\Core\Loader\Interface\LoaderAwareInterface;
use Prison\Core\Loader\Loader;
use Prison\Core\Loader\Trait\LoaderAwareTrait;
use Prison\Core\Logger\Trait\LoggerTrait;
use Prison\Core\Validator\CommandValidator;
use Prison\Core\Validator\Constraints\Validator\Validator;
use Prison\Permission\PermissionList;
use Prison\Permission\Validator\Constraints\ListPermissionsConstraint;

class ListPermissionsCommand extends Command implements LoaderAwareInterface
{
    use LoaderAwareTrait;
    use LoggerTrait;

    public function __construct(Loader $loader)
    {
        $this->setLoader($loader);

        parent::__construct(
            'listpermissions',
            'List permissions for the specified player name',
            '/listpermissions <playername>',
            ['listperms']
        );

        $this->setPermission(PermissionList::LIST_PERMISSIONS_COMMAND);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        $commandValidator = new CommandValidator($sender);

        if (!$commandValidator->isValid($args, new ListPermissionsConstraint($this->loader))) {
            $this->sendInfo($sender, $this->getUsage());

            return;
        }

        [$playerName] = $args;

        $player = $this->loader->getServer()->getOfflinePlayer($playerName);

        if (!$player instanceof Player) {
            return;
        }

        foreach ($player->getEffectivePermissions() as $effectivePermission) {
            $sender->sendMessage($effectivePermission->getPermission());
        }
    }
}