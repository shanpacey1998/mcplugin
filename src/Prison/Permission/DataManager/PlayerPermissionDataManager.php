<?php

declare(strict_types=1);

namespace Prison\Permission\DataManager;

use pocketmine\player\IPlayer;
use Prison\Core\DataManager\Trait\FilesystemTrait;
use Prison\Core\Loader\Loader;
use Prison\Core\Loader\Trait\LoaderAwareTrait;
use Prison\Core\Logger\Trait\LoggerTrait;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;

class PlayerPermissionDataManager implements PlayerPermissionDataManagerInterface
{
    use FilesystemTrait;
    use LoaderAwareTrait;
    use LoggerTrait;

    private const FOLDER_NAME = 'permissions';

    public function __construct(Loader $loader) {
        $this->setFilesystem(new Filesystem());

        $this->setLoader($loader);
    }

    public function savePermissions(IPlayer $player, array $permissions): void
    {
        $filePath = $this->getFilepath($player);

        if (!$this->filesystem->exists($filePath)) {
            $this->filesystem->touch($filePath);
        }

        $successful = file_put_contents($filePath, json_encode($permissions, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT));

        if (false !== $successful) {
            $this->logInfo(sprintf('Updated permissions for player: %s', $player->getName()));
        }
    }

    public function getPermissions(IPlayer $player): array
    {
        $filePath = $this->getFilepath($player);

        if (!$this->filesystem->exists($filePath)) {
            $this->filesystem->touch($filePath);
            file_put_contents($filePath, json_encode([], JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT));
        }

        return json_decode(file_get_contents($filePath), true, 512, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT);
    }

    public function getDirectoryPath(): string
    {
        return Path::join($this->loader->getDataFolder(), self::FOLDER_NAME);
    }

    private function getJsonFilename(IPlayer $player): string
    {
        return sprintf('%s.json', $player->getName());
    }

    private function getFilepath(IPlayer $player): string
    {
        return Path::join($this->getDirectoryPath(), $this->getJsonFilename($player));
    }
}