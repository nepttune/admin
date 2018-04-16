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

    /** @var \Kdyby\Redis\RedisStorage */
    private $cacheStorage;

    public function injectRestricted(
        \Nepttune\Model\Authorizator $authorizator,
        \Kdyby\Redis\RedisStorage $redisStorage) : void
    {
        $this->authorizator = $authorizator;
        $this->cacheStorage = $redisStorage;
    }

    public function isAllowed(int $userId, string $action) : bool
    {
        $cache = new \Nette\Caching\Cache($this->cacheStorage);

        if (!$cache->call([static::class, 'isRestricted'], $action))
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
