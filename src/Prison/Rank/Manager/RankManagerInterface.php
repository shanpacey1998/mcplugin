<?php

declare(strict_types=1);

namespace Prison\Rank\Manager;

use Prison\Core\Loader\Interface\LoaderAwareInterface;
use Prison\Rank\Dto\Rank;

interface RankManagerInterface extends LoaderAwareInterface
{
    public function getDefaultRank(): ?Rank;

    /**
     * @return Rank[]
     */
    public function getRanksByOrder(): array;

    /**
     * @return Rank[]
     */
    public function getRanksByName(): array;

    public function getRank(string $name): ?Rank;

    public function getNextRank(Rank $rank): ?Rank;

    public function resyncRanks(): void;

    public function generateRanks(): void;
}
