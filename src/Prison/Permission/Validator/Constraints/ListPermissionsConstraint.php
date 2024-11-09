<?php

declare(strict_types=1);

namespace Prison\Permission\Validator\Constraints;

use pocketmine\player\Player;
use Prison\Core\Loader\Interface\LoaderAwareInterface;
use Prison\Core\Loader\Loader;
use Prison\Core\Loader\Trait\LoaderAwareTrait;
use Prison\Core\Validator\Constraints\ConstraintInterface;

class ListPermissionsConstraint implements ConstraintInterface, LoaderAwareInterface
{
    use LoaderAwareTrait;

    public function __construct(Loader $loader)
    {
        $this->setLoader($loader);
    }

    public function validate(mixed $value): ?string
    {
        if (!is_array($value)) {
            return 'Invalid arguments passed';
        }

        if (0 === count($value)) {
            return 'Missing player name';
        }

        if (1 < count($value)) {
            return 'Expecting only one argument';
        }

        [$playerName] = $value;

        $player = $this->loader->getServer()->getPlayerExact($playerName);

        if (!$player instanceof Player) {
            return sprintf('Could not find the player %s', $playerName);
        }

        return null;
    }
}