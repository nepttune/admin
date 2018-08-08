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
    public function injectRestricted(\Nette\DI\Container $container, \Nette\Caching\IStorage $storage) : void;

    public function isAllowed() : bool;

    public function getRestricted() : array;
}

