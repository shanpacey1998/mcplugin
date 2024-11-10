<?php

declare(strict_types=1);

namespace Prison\Core\Validator;

use pocketmine\command\CommandSender;
use Prison\Core\Logger\Trait\LoggerTrait;
use Prison\Core\Validator\Constraints\ConstraintInterface;
use Prison\Core\Validator\Constraints\Validator\Validator;

class CommandValidator
{
    use LoggerTrait;

    public function __construct(private CommandSender $sender)
    {
    }

    public function isValid(array $args, ConstraintInterface $constraint): bool
    {
        $validator = new Validator();
        $errors = $validator->validate($args, [$constraint]);

        if (count($errors) > 0) {
            $this->sendErrors($this->sender, $errors);

            return false;
        }

        return true;
    }
}
