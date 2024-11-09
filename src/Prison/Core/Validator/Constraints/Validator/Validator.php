<?php

namespace Prison\Core\Validator\Constraints\Validator;

use Prison\Core\Validator\Constraints\ConstraintInterface;

class Validator implements ValidateInterface
{
    /**
     * {@inheritdoc}
     */
    public function validate(mixed $value, array $constraints): array
    {
        $errors = [];

        foreach ($constraints as $constraint) {
            if ($constraint instanceof ConstraintInterface) {
                $error = $constraint->validate($value);

                if ($error !== null) {
                    $errors[] = $error;
                }
            }
        }

        return $errors;
    }
}