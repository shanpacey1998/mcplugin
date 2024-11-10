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
use Prison\Permission\Manager\PlayerPermissionManagerInterface;
use Prison\Permission\PermissionList;
use Prison\Permission\Validator\Constraints\RemovePermissionConstraint;

class RemovePermissionCommand extends Command implements LoaderAwareInterface
{
    use LoaderAwareTrait;
    use LoggerTrait;

    public function __construct(
        private readonly PlayerPermissionManagerInterface $playerPermissionManager,
        Loader $loader
    )
    {
        $this->setLoader($loader);

        parent::__construct(
            'removepermission',
            'Removes a permission from the specified player',
            '/removepermission <playername> <permission>',
            ['remperm', 'removeperm', 'unsetpermission', 'unsetperm']
        );

        $this->setPermission(PermissionList::REMOVE_PERMISSION_COMMAND);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        $commandValidator = new CommandValidator($sender);

        if (!$commandValidator->isValid($args, new RemovePermissionConstraint($this->loader))) {
            $this->sendInfo($sender, $this->getUsage());

            return;
        }

        [$playerName, $permission] = $args;

        $player = $this->loader->getServer()->getOfflinePlayer($playerName);

        if (!$player instanceof Player) {
            return;
        }

        $this->playerPermissionManager->removePermission($player, $permission);

        $this->sendSuccess($sender, sprintf('Successfully removed permission %s from %s', $permission, $player->getName()));
    }
}