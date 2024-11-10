<?php

declare(strict_types=1);

namespace Prison\Rank\DataManager;

use Prison\Core\DataManager\Trait\FilesystemTrait;
use Prison\Core\Loader\Loader;
use Prison\Core\Loader\Trait\LoaderAwareTrait;
use Prison\Core\Logger\Trait\LoggerTrait;
use Prison\Rank\Dto\Rank;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;

class RankDataManager implements RankDataManagerInterface
{
    use FilesystemTrait;
    use LoaderAwareTrait;
    use LoggerTrait;

    public const FOLDER_NAME = 'ranks';

    public function __construct(Loader $loader) {
        $this->setFilesystem(new Filesystem());
        $this->setLoader($loader);
    }

    public function getRanks(): array
    {
        $filePath = $this->getFilepath();

        if (!$this->filesystem->exists($filePath)) {
            $this->filesystem->touch($filePath);

            file_put_contents($filePath, json_encode([], JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT));
        }

        $ranks = json_decode(file_get_contents($filePath), true, 512, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT);
        $indexedRanks = [];

        foreach ($ranks as $rankToIndex) {
            $rank = Rank::fromJson($rankToIndex);

            $indexedRanks[$rank->getOrder()] = $rank;
        }

        return $indexedRanks;
    }

    public function getRank(string $name): ?Rank
    {
        $ranks = $this->getRanks();

        foreach ($ranks as $rank) {
            if ($name === $rank->getName()) {
                return $rank;
            }
        }

        return null;
    }

    public function addRank(Rank $rank): void
    {
        $ranks = $this->getRanks();

        if (isset($ranks[$rank->getOrder()])) {
            $this->logWarning(sprintf('Rank %s already exists with order %d', $rank->getName(), $rank->getOrder()));

            return;
        }

        $ranks[$rank->getOrder()] = $rank;

        file_put_contents($this->getFilepath(), json_encode($ranks, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT));
    }

    public function getDirectoryPath(): string
    {
        return Path::join($this->loader->getDataFolder(), self::FOLDER_NAME);
    }

    private function getJsonFilename(): string
    {
        return 'ranks.json';
    }

    private function getFilepath(): string
    {
        return Path::join($this->getDirectoryPath(), $this->getJsonFilename());
    }
}