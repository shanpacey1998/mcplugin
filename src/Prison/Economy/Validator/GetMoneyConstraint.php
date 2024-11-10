<?php

namespace Prison\Economy\Validator;

use pocketmine\player\Player;
use Prison\Core\Loader\Interface\LoaderAwareInterface;
use Prison\Core\Loader\Loader;
use Prison\Core\Loader\Trait\LoaderAwareTrait;
use Prison\Core\Validator\Constraints\ConstraintInterface;

class GetMoneyConstraint implements ConstraintInterface, LoaderAwareInterface
{
    use LoaderAwareTrait;

    /**
     * @param Loader $loader
     */
    public function __construct(Loader $loader)
    {
        $this->setLoader($loader);
    }

    public function validate(mixed $value): ?string
    {
        if (!is_array($value)) {
            return 'Invalid arguments passed';
        }

        if (1 !== count($value)) {
            return 'Invalid number of arguments passed';
        }

        [$playerName] = $value;

        $player = $this->loader->getServer()->getOfflinePlayer($playerName);

        if (!$player instanceof Player) {
            return sprintf('Could not find the player %s', $playerName);
        }

        return null;
    }
}