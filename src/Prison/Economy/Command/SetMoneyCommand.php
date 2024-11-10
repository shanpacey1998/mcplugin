<?php

namespace Prison\Economy\Command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use Prison\Core\Loader\Interface\LoaderAwareInterface;
use Prison\Core\Loader\Trait\LoaderAwareTrait;
use Prison\Core\Logger\Trait\LoggerTrait;

class SetMoneyCommand extends Command implements LoaderAwareInterface
{
    use LoaderAwareTrait;
    use LoggerTrait;

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        // TODO: Implement execute() method.
    }
}