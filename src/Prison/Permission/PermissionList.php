<?php

namespace Prison\Permission;

use pocketmine\permission\PermissionParser;

class PermissionList
{
    public const LIST_PERMISSIONS_COMMAND = 'prison.command.permissions.list';
    public const ADD_PERMISSION_COMMAND = 'prison.command.permissions.add';
    public const REMOVE_PERMISSION_COMMAND = 'prison.command.permissions.remove';
    public const ADD_MONEY_COMMAND = 'prison.command.money.add';

    public const PERMISSIONS_MAP = [
        self::LIST_PERMISSIONS_COMMAND => PermissionParser::DEFAULT_OP,
        self::ADD_PERMISSION_COMMAND => PermissionParser::DEFAULT_OP,
        self::REMOVE_PERMISSION_COMMAND => PermissionParser::DEFAULT_OP,
        self::ADD_MONEY_COMMAND => PermissionParser::DEFAULT_OP,
    ];
}