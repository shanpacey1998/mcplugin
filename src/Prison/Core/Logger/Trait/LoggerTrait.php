<?php

declare(strict_types=1);

namespace Prison\Core\Logger\Trait;

use Logger;
use Prison\Core\Loader\Interface\LoaderAwareInterface;

trait LoggerTrait
{
    private function logInfo(string $message): void
    {
        if ($this->isDebug()) {
            $this->getLogger()?->info($message);
        }
    }

    private function logWarning(string $message): void
    {
        if ($this->isDebug()) {
            $this->getLogger()?->warning($message);
        }
    }

    private function getLogger(): ?Logger
    {
        return $this instanceof LoaderAwareInterface
            ? $this->getLoader()->getLogger()
            : null;
    }

    private function isDebug(): bool
    {
        return $this instanceof LoaderAwareInterface && $this->getLoader()->isDebug();
    }
}