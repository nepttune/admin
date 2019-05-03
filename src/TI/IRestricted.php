<?php

/**
 * This file is part of Nepttune (https://www.peldax.com)
 *
 * Copyright (c) 2018 Václav Pelíšek (info@peldax.com)
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the MIT license. For more information, see
 * <https://www.peldax.com>.
 */

declare(strict_types = 1);

namespace Nepttune\TI;

interface IRestricted
{
    public const LOGIN_WALL = true;
    
    public function decorateRestricted(\Nette\DI\Container $container, \Nette\Caching\IStorage $storage) : void;

    public function getRestricted() : array;
    
    public function getAuthorizator() : \Nepttune\Model\Authorizator;
    
    public static function getRestrictedStatic() : array;
}

