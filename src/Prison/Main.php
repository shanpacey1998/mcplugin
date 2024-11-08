<?php

declare(strict_types=1);

namespace Prison;

use pocketmine\plugin\PluginBase;

class Main extends PluginBase
{
    protected function onEnable(): void
    {
        $this->getLogger()->info('PrisonCore Enabled.');
    }
}
