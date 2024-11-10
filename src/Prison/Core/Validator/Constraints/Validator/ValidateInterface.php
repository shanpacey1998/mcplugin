<?php

declare(strict_types=1);

namespace Prison\Core\Validator\Constraints\Validator;

use Prison\Core\Validator\Constraints\ConstraintInterface;

interface ValidateInterface
{
    /**
     * Validates the value against a set of constraints.
     *
     * @param mixed $value The value to validate
     * @param ConstraintInterface[] $constraints The constraints to apply
     *
     * @return array An array of validation errors, if any
     */
    public function validate(mixed $value, array $constraints): array;
}
