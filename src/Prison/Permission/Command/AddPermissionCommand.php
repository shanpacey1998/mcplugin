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
use Prison\Permission\Manager\PlayerPermissionManagerInterface;
use Prison\Permission\PermissionList;
use Prison\Permission\Validator\Constraints\AddPermissionConstraint;
use Prison\Permission\Validator\Constraints\ListPermissionsConstraint;

class AddPermissionCommand extends Command implements LoaderAwareInterface
{
    use LoaderAwareTrait;
    use LoggerTrait;

    public function __construct(
        private PlayerPermissionManagerInterface $playerPermissionManager,
        Loader $loader
    )
    {
        $this->setLoader($loader);

        parent::__construct(
            'addpermission',
            'Adds a permission to the specified player',
            '/addpermission <playername> <permission>',
            ['addperm', 'setpermission', 'setperm']
        );

        $this->setPermission(PermissionList::ADD_PERMISSION_COMMAND);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        $commandValidator = new CommandValidator($sender);

        if (!$commandValidator->isValid($args, new AddPermissionConstraint($this->loader))) {
            $this->sendInfo($sender, $this->getUsage());

            return;
        }

        [$playerName, $permission] = $args;

        $player = $this->loader->getServer()->getOfflinePlayer($playerName);

        if (!$player instanceof Player) {
            return;
        }

        $this->playerPermissionManager->addPermission($player, $permission);

        $this->sendSuccess($sender, sprintf('Successfully added permission %s to %s', $permission, $player->getName()));
    }
}