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
use Prison\Core\Validator\Constraints\Validator\Validator;
use Prison\Permission\Manager\PlayerPermissionManagerInterface;
use Prison\Permission\PermissionList;
use Prison\Permission\Validator\Constraints\AddPermissionConstraint;

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

        $this->setPermission(PermissionList::LIST_PERMISSIONS_COMMAND);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        $validator = new Validator();
        $errors = $validator->validate($args, [new AddPermissionConstraint($this->loader)]);

        if (count($errors) > 0) {
            $this->sendErrors($sender, $errors);
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