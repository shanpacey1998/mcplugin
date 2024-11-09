<?php

declare(strict_types=1);

namespace Prison\Core\Loader;

use pocketmine\permission\PermissionAttachment;
use pocketmine\plugin\PluginBase;
use Prison\Permission\DataManager\PlayerPermissionDataManager;
use Prison\Permission\EventListener\PlayerPermissionListener;
use Prison\Permission\Manager\PlayerPermissionManager;
use Prison\Permission\Manager\PlayerPermissionManagerInterface;
use Symfony\Component\Filesystem\Filesystem;

class Loader extends PluginBase
{
    private bool $isDebug = false;

    /**
     * @var PermissionAttachment[]
     */
    private array $attachments;

    private PlayerPermissionManagerInterface $playerPermissionManager;

    protected function onEnable(): void
    {
        $fileSystem = new Filesystem();
        $fileSystem->mkdir($this->getDataFolder());

        $this->attachments = [];

        $playerPermissionDataManager = new PlayerPermissionDataManager($this);
        $playerPermissionDataManager->createDirectory();

        $this->playerPermissionManager = new PlayerPermissionManager(
            $playerPermissionDataManager,
            $this
        );
        $this->playerPermissionManager->registerPlayers();

        $this->getServer()->getPluginManager()->registerEvents(new PlayerPermissionListener($this->playerPermissionManager), $this);

        $this->getLogger()->info('PrisonCore Enabled.');
    }

    protected function onDisable(): void
    {
        $this->playerPermissionManager->unregisterPlayers();
    }

    /**
     * @return PermissionAttachment[]
     */
    public function getAttachments(): array
    {
        return $this->attachments;
    }

    public function setAttachment(string $userUuid, PermissionAttachment $attachment): void
    {
        $this->attachments[$userUuid] = $attachment;
    }

    public function removeAttachment(string $userUuid): void
    {
        if ($this->hasAttachment($userUuid)) {
            unset($this->attachments[$userUuid]);
        }
    }

    public function hasAttachment(string $userUuid): bool
    {
        return isset($this->attachments[$userUuid]);
    }

    public function getAttachment(string $userUuid): ?PermissionAttachment
    {
        return $this->attachments[$userUuid] ?? null;
    }

    public function isDebug(): bool
    {
        return $this->isDebug;
    }
}
