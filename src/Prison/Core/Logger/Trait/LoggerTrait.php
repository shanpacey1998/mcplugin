<?php

declare(strict_types=1);

namespace Prison\Core\Logger\Trait;

use Logger;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;
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

    private function sendSuccess(CommandSender $sender, string $message): void
    {
        $sender->sendMessage(sprintf('%s%s', TextFormat::GREEN, $message));
    }

    private function sendInfo(CommandSender $sender, string $message): void
    {
        $sender->sendMessage(sprintf('%s%s', TextFormat::GRAY, $message));
    }

    private function sendError(CommandSender $sender, string $message): void
    {
        $sender->sendMessage(sprintf('%s%s', TextFormat::RED, $message));
    }

    private function sendErrors(CommandSender $sender, array $messages): void
    {
        foreach ($messages as $message) {
            $this->sendError($sender, $message);
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