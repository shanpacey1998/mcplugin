<?php

declare(strict_types=1);

namespace Prison\Rank\Dto;

class Rank implements \JsonSerializable
{
    /**
     * @param string[] $permissions
     */
    public function __construct(
        private int $order,
        private string $name,
        private int $rankUpCost,
        private array $permissions,
    ) {
    }

    public function getOrder(): int
    {
        return $this->order;
    }

    public function setOrder(int $order): void
    {
        $this->order = $order;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getRankUpCost(): int
    {
        return $this->rankUpCost;
    }

    public function setRankUpCost(int $rankUpCost): void
    {
        $this->rankUpCost = $rankUpCost;
    }

    /**
     * @param string[] $permissions
     */
    public function setPermissions(array $permissions): void
    {
        $this->permissions = $permissions;
    }

    /**
     * @return string[]
     */
    public function getPermissions(): array
    {
        return $this->permissions;
    }

    public static function fromJson(array $data): self
    {
        return new self(
            $data['order'],
            $data['rankName'],
            $data['rankUpCost'],
            $data['permissions']
        );
    }

    public function jsonSerialize(): array
    {
        return [
            'order' => $this->order,
            'rankName' => $this->name,
            'rankUpCost' => $this->rankUpCost,
            'permissions' => $this->permissions,
        ];
    }
}
