<?php

declare(strict_types=1);

namespace Prison\Rank\DataManager;

use pocketmine\player\IPlayer;
use pocketmine\player\Player;
use Prison\Core\DataManager\Trait\FilesystemTrait;
use Prison\Core\Loader\Loader;
use Prison\Core\Loader\Trait\LoaderAwareTrait;
use Prison\Core\Logger\Trait\LoggerTrait;
use Prison\Rank\Dto\Rank;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;

class PlayerRankDataManager implements PlayerRankDataManagerInterface
{
    use FilesystemTrait;
    use LoaderAwareTrait;
    use LoggerTrait;

    private const FOLDER_NAME = 'players';

    public function __construct(Loader $loader)
    {
        $this->setFilesystem(new Filesystem());
        $this->setLoader($loader);
    }

    public function getDirectoryPath(): string
    {
        return Path::join($this->loader->getDataFolder(), RankDataManager::FOLDER_NAME, self::FOLDER_NAME);
    }

    public function getRankName(Player $player): ?string
    {
        $filePath = $this->getFilepath($player);

        if (!$this->filesystem->exists($filePath)) {
            $this->filesystem->touch($filePath);
            file_put_contents($filePath, json_encode([], JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT));
        }

        $rankData = json_decode(file_get_contents($filePath), true, 512, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT);

        return $rankData['rankName'] ?? null;
    }

    public function saveRank(Player $player, Rank $rank): void
    {
        $filePath = $this->getFilepath($player);

        if (!$this->filesystem->exists($filePath)) {
            $this->filesystem->touch($filePath);
        }

        $rankData = ['rankName' => $rank->getName()];

        $successful = file_put_contents($filePath, json_encode($rankData, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT));

        if (false !== $successful) {
            $this->logInfo(sprintf('Updated rank for player: %s to %s', $player->getName(), $rank->getName()));
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
}
