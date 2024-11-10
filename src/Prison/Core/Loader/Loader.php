<?php

declare(strict_types=1);

namespace Prison\Core\Loader;

use pocketmine\permission\DefaultPermissions;
use pocketmine\permission\Permission;
use pocketmine\permission\PermissionAttachment;
use pocketmine\permission\PermissionManager;
use pocketmine\permission\PermissionParser;
use pocketmine\plugin\PluginBase;
use Prison\Permission\Command\AddPermissionCommand;
use Prison\Permission\Command\ListAllPermissionsCommand;
use Prison\Permission\Command\ListPermissionsCommand;
use Prison\Permission\Command\RemovePermissionCommand;
use Prison\Permission\DataManager\PlayerPermissionDataManager;
use Prison\Permission\EventListener\PlayerPermissionListener;
use Prison\Permission\Manager\PlayerPermissionManager;
use Prison\Permission\Manager\PlayerPermissionManagerInterface;
use Prison\Permission\PermissionList;
use Prison\Rank\DataManager\PlayerRankDataManager;
use Prison\Rank\DataManager\RankDataManager;
use Prison\Rank\Manager\RankManager;
use Symfony\Component\Filesystem\Filesystem;

class Loader extends PluginBase
{
    private const FALLBACK_PREFIX = 'prison';

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

        $this->registerDataManagers();

        $this->attachments = [];

        $this->getServer()->getPluginManager()->registerEvents(new PlayerPermissionListener($this->playerPermissionManager), $this);

        $this->getServer()->getCommandMap()->registerAll(
            self::FALLBACK_PREFIX,
            [
                new ListPermissionsCommand($this),
                new AddPermissionCommand($this->playerPermissionManager, $this),
                new RemovePermissionCommand($this->playerPermissionManager, $this),
                new ListAllPermissionsCommand(),
            ]
        );

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

    private function registerPermissions(array $permissionsToRegister): void
    {
        $permissionManager = PermissionManager::getInstance();

        foreach ($permissionsToRegister as $permission => $default) {
            if (null !== $permissionManager->getPermission($permission)) {
                $this->getServer()->getLogger()->critical(sprintf('Permission "%s" already registered.', $permission));

                return;
            }
        }

        $opRoot = $permissionManager->getPermission(DefaultPermissions::ROOT_OPERATOR);
        $everyoneRoot = $permissionManager->getPermission(DefaultPermissions::ROOT_USER);

        if (null === $opRoot || null === $everyoneRoot) {
            $this->getServer()->getLogger()->critical('OP Root/Everyone Root not loaded.');

            return;
        }

        foreach ($permissionsToRegister as $permission => $default) {
            $parsedPermission = new Permission($permission);
            $permissionManager->addPermission($parsedPermission);

            switch ($default) {
                case PermissionParser::DEFAULT_TRUE:
                    $everyoneRoot->addChild($parsedPermission->getName(), true);

                    break;
                case PermissionParser::DEFAULT_OP:
                    $opRoot->addChild($parsedPermission->getName(), true);

                    break;
                case PermissionParser::DEFAULT_NOT_OP:
                    $everyoneRoot->addChild($parsedPermission->getName(), true);
                    $opRoot->addChild($parsedPermission->getName(), false);

                    break;
            }
        }
    }

    private function registerDataManagers(): void
    {
        $playerPermissionDataManager = new PlayerPermissionDataManager($this);
        $playerPermissionDataManager->createDirectory();

        $rankDataManager = new RankDataManager($this);
        $rankDataManager->createDirectory();

        $playerRankDataManager = new PlayerRankDataManager($this);
        $playerRankDataManager->createDirectory();

        $rankManager = new RankManager($rankDataManager, $this);
        $ranks = $rankManager->getRanksByOrder();
        $rankPermissions = [];

        foreach ($ranks as $rank) {
            foreach ($rank->getPermissions() as $permission) {
                $rankPermissions[$permission] = PermissionParser::DEFAULT_OP;
            }
        }

        $this->playerPermissionManager = new PlayerPermissionManager(
            $playerPermissionDataManager,
            $this
        );

        $this->registerPermissions(PermissionList::PERMISSIONS_MAP);
        $this->registerPermissions($rankPermissions);

        $this->playerPermissionManager->registerPlayers();
    }
}
