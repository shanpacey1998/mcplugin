<?php

namespace Prison\Economy\Command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\Server;
use Prison\Core\Loader\Interface\LoaderAwareInterface;
use Prison\Core\Loader\Loader;
use Prison\Core\Loader\Trait\LoaderAwareTrait;
use Prison\Core\Logger\Trait\LoggerTrait;
use Prison\Core\Validator\CommandValidator;
use Prison\Economy\Manager\EconomyManagerInterface;
use Prison\Economy\Validator\GetMoneyConstraint;
use Prison\Economy\Validator\MoneyConstraint;
use Prison\Permission\PermissionList;

class MyMoneyCommand extends Command implements LoaderAwareInterface
{
    use LoaderAwareTrait;
    use LoggerTrait;

    public function __construct(
        private readonly EconomyManagerInterface $economyManager,
        Loader                                   $loader
    ) {
        $this->setLoader($loader);

        parent::__construct(
            'mymoney',
            'Outputs the current player\'s balance',
            '/mymoney',
            ['mybalance', 'mybal', 'mymoney']
        );

        $this->setPermission(PermissionList::MY_MONEY_COMMAND);
    }
    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        $commandValidator = new CommandValidator($sender);

        if (!$commandValidator->isValid($args, new GetMoneyConstraint($this->loader))
            && !$sender instanceof Server) {
            $this->sendInfo($sender, $this->getUsage());

            return;
        }
        $playerName = $sender->getName();

        $player = $this->loader->getServer()->getOfflinePlayer($playerName);

        if (!$player instanceof Player) {
            return;
        }

        $balance = $this->economyManager->getMoney($player->getName());

        $this->sendSuccess($sender, sprintf('%s\'s balance is %d', $player->getName(), $balance));
    }
}
