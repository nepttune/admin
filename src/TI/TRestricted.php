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

trait TRestricted
{
    /** @var \Nepttune\Model\Authorizator */
    private $authorizator;

    /** @var \Nette\Caching\Cache */
    private $cache;

    public function injectRestricted(
        \Nepttune\Model\Authorizator $authorizator,
        \Nette\Caching\IStorage $storage) : void
    {
        $this->authorizator = $authorizator;
        $this->cache = new \Nette\Caching\Cache($storage);
    }

    public function isAllowed(int $userId, string $action) : bool
    {
        if (!$this->cache->call([static::class, 'isRestricted'], $action))
        {
            return true;
        }

        $resource = $this instanceof \Nette\Application\IPresenter ?
            $this->getName() :
            static::getReflection()->getName();

        return $this->authorizator->isAllowed($userId, $resource, $action);
    }

    public static function isRestricted(string $action) : bool
    {
        $action = 'action' . ucfirst($action);
        $handle = 'handle' . ucfirst($action);

        /** @var \Nette\Application\UI\ComponentReflection $reflection */
        $reflection = static::getReflection();

        return ($reflection->hasMethod($action) && $reflection->getMethod($action)->hasAnnotation('restricted')) ||
               ($reflection->hasMethod($handle) && $reflection->getMethod($handle)->hasAnnotation('restricted'));
    }
}
