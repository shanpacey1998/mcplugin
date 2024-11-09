<?php

namespace Prison\Permission;

use pocketmine\permission\PermissionParser;

class PermissionList
{
    public const LIST_PERMISSIONS_COMMAND = 'prison.command.permissions.list';

    public const PERMISSIONS_MAP = [
        self::LIST_PERMISSIONS_COMMAND => PermissionParser::DEFAULT_OP,
    ];
}