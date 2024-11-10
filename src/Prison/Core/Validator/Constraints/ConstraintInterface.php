<?php

declare(strict_types=1);

namespace Prison\Core\Validator\Constraints;

interface ConstraintInterface
{
    /**
     * Validates a value according to the constraint.
     *
     * @param mixed $value The value to validate
     *
     * @return string|null The error message, or null if valid
     */
    public function validate(mixed $value): ?string;
}
