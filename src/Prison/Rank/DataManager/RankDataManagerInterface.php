<?php

declare(strict_types=1);

namespace Prison\Rank\DataManager;

use Prison\Core\DataManager\DataManagerInterface;
use Prison\Core\Loader\Interface\LoaderAwareInterface;
use Prison\Rank\Dto\Rank;

interface RankDataManagerInterface extends LoaderAwareInterface, DataManagerInterface
{
    /**
     * @return Rank[]
     */
    public function getRanks(): array;

    public function getRank(string $name): ?Rank;

    public function addRank(Rank $rank): void;
}