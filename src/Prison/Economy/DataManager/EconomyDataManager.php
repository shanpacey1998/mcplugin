<?php

declare(strict_types=1);

namespace Prison\Economy\DataManager;

use pocketmine\player\IPlayer;
use Prison\Core\DataManager\DataManagerInterface;
use Prison\Core\DataManager\Trait\FilesystemTrait;
use Prison\Core\Loader\Loader;
use Prison\Core\Loader\Trait\LoaderAwareTrait;
use Prison\Core\Logger\Trait\LoggerTrait;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;

class EconomyDataManager implements EconomyDataManagerInterface, DataManagerInterface
{
    use LoaderAwareTrait;
    use LoggerTrait;
    use FilesystemTrait;

    private const FOLDER_NAME = 'economy';

    public function __construct(Loader $loader)
    {
        $this->filesystem = new Filesystem();

        $this->setLoader($loader);
    }

    public function getMoney(IPlayer $player): array
    {
        $filePath = $this->getFilepath($player);

        if (!$this->filesystem->exists($filePath)) {
            $this->filesystem->touch($filePath);
            file_put_contents($filePath, json_encode([], JSON_THROW_ON_ERROR));
        }

        return json_decode(file_get_contents($filePath), true, 512, JSON_THROW_ON_ERROR);
    }

    public function saveMoney(IPlayer $player, array $money): void
    {
        $filePath = $this->getFilepath($player);

        if (!$this->filesystem->exists($filePath)) {
            $this->filesystem->touch($filePath);
        }

        $successful = file_put_contents($filePath, json_encode($money, JSON_THROW_ON_ERROR));

        if (false !== $successful) {
            $this->logInfo(sprintf('Updated balance for player: %s', $player->getName()));
        }
    }

    private function getJsonFilename(IPlayer $player): string
    {
        return sprintf('%s.json', $player->getName());
    }

    private function getFilepath(IPlayer $player): string
    {
        return Path::join($this->getDirectoryPath(), $this->getJsonFilename($player));
    }

    public function getDirectoryPath(): string
    {
        return Path::join($this->loader->getDataFolder(), self::FOLDER_NAME);
    }
}
