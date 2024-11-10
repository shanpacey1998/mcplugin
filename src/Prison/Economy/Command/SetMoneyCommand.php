<?php

declare(strict_types=1);

namespace Prison\Economy\Command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use Prison\Core\Loader\Interface\LoaderAwareInterface;
use Prison\Core\Loader\Loader;
use Prison\Core\Loader\Trait\LoaderAwareTrait;
use Prison\Core\Logger\Trait\LoggerTrait;
use Prison\Core\Validator\CommandValidator;
use Prison\Economy\Manager\EconomyManagerInterface;
use Prison\Economy\Validator\MoneyConstraint;
use Prison\Permission\PermissionList;

class SetMoneyCommand extends Command implements LoaderAwareInterface
{
    use LoaderAwareTrait;
    use LoggerTrait;

    public function __construct(
        private readonly EconomyManagerInterface $economyManager,
        Loader                                   $loader
    ) {
        $this->setLoader($loader);

        parent::__construct(
            'setmoney',
            'Overrides the specified player\'s balance and sets it to the given amount',
            '/setmoney <playername> <amount>',
            ['setbalance', 'setbal', 'setmoney']
        );

        $this->setPermission(PermissionList::SET_MONEY_COMMAND);
    }
    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        $commandValidator = new CommandValidator($sender);

        if (!$commandValidator->isValid($args, new MoneyConstraint($this->loader))) {
            $this->sendInfo($sender, $this->getUsage());

            return;
        }

        [$playerName, $amount] = $args;

        $player = $this->loader->getServer()->getOfflinePlayer($playerName);

        if (!$player instanceof Player) {
            return;
        }

        $this->economyManager->setMoney($player->getName(), (int) $amount);

        $this->sendSuccess($sender, sprintf('Successfully set %s\'s balance to %d', $player->getName(), $amount));
    }
}
