<?php

namespace Prison\Rank\Manager;

use Prison\Core\Loader\Loader;
use Prison\Core\Loader\Trait\LoaderAwareTrait;
use Prison\Core\Logger\Trait\LoggerTrait;
use Prison\Rank\DataManager\RankDataManagerInterface;
use Prison\Rank\Dto\Rank;

class RankManager implements RankManagerInterface
{
    use LoaderAwareTrait;
    use LoggerTrait;

    public function __construct(
        private readonly RankDataManagerInterface $rankDataManager,
        Loader $loader,
    ) {
        $this->setLoader($loader);
    }

    public const RANK_BASE_PERMISSION = 'prison.ranks.rank.%s';

    /**
     * @var Rank[]
     */
    private static array $ranksIndexedByOrder = [];

    /**
     * @var Rank[]
     */
    private static array $ranksIndexedByName = [];

    public function getDefaultRank(): ?Rank
    {
        return $this->getRanksByOrder()[0] ?? null;
    }

    public function getRanksByOrder(): array
    {
        if (!empty(self::$ranksIndexedByOrder)) {
            return self::$ranksIndexedByOrder;
        }

        $ranks = $this->rankDataManager->getRanks();

        if (empty($ranks)) {
            $this->resyncRanks();
        }

        return self::$ranksIndexedByOrder;
    }

    public function getRanksByName(): array
    {
        if (!empty(self::$ranksIndexedByName)) {
            return self::$ranksIndexedByName;
        }

        $ranks = $this->rankDataManager->getRanks();

        if (empty($ranks)) {
            $this->resyncRanks();
        }

        return self::$ranksIndexedByName;
    }

    public function getRank(string $name): ?Rank
    {
        return $this->getRanksByName()[$name] ?? null;
    }

    public function getNextRank(Rank $rank): ?Rank
    {
        return $this->getRanksByOrder()[$rank->getOrder() + 1] ?? null;
    }

    public function resyncRanks(): void
    {
        $ranks = $this->rankDataManager->getRanks();

        if (empty($ranks)) {
            $this->generateRanks();

            $ranks = $this->rankDataManager->getRanks();
        }

        $ranksIndexedByName = [];

        foreach ($ranks as $rank) {
            $ranksIndexedByName[$rank->getName()] = $rank;
        }

        self::$ranksIndexedByOrder = $ranks;
        self::$ranksIndexedByName = $ranksIndexedByName;
    }

    public function generateRanks(): void
    {
        $ranks = $this->rankDataManager->getRanks();

        if (!empty($ranks)) {
            return;
        }

        $this->logInfo('Ranks not found. Generating default ranks...');

        $displayOrder = 0;
        $rankUpCost = 999999999999999999;

        foreach (range('A', 'Z') as $char) {
            $rank = new Rank(
                $displayOrder,
                $char,
                $rankUpCost,
                [sprintf(self::RANK_BASE_PERMISSION, $char)]
            );

            $this->rankDataManager->addRank($rank);

            $this->logInfo(sprintf('Generated rank %s with order %d', $rank->getName(), $rank->getOrder()));

            $displayOrder++;
        }

        $this->resyncRanks();
    }
}