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
        $this->cache = new \Nette\Caching\Cache($storage, 'Nepttune.Authorizator');
    }

    public function isAllowed() : bool
    {
        $user = $this->getUser();

        /** User has access to everything */
        if ($user->isInRole('root'))
        {
            return true;
        }

        /** Action is not restricted */
        if (!static::isRestricted($this->getAction()))
        {
            return true;
        }

        return $this->authorizator->isAllowed($user->getId(), $this->getAction(true));
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
    
    public static function getRestricted() : array
    {
        $pages = [];

        /** @var \Nette\Application\UI\ComponentReflection $reflection */
        $reflection = static::getReflection();

        foreach ($reflection->getMethods(\ReflectionMethod::IS_PUBLIC) as $method)
        {
            if ($method->isStatic() ||
                !$method->isPublic() ||
                !$method->hasAnnotation('restricted') ||
                !\in_array(substr($method->name, 0, 6), ['action', 'handle'], true))
            {
                continue;
            }

            $regex = '/App\\\\([A-Z][a-z]*)Module\\\\Presenter\\\\([A-Z][a-z]*)Presenter/';
            $matches = [];
            preg_match($regex, $reflection->name, $matches);

            if (\count($matches) < 3)
            {
                continue;
            }

            $pages[] = ":{$matches[1]}:{$matches[2]}:" . lcfirst(substr($method->name, 6));
        }

        return $pages;
    }
}
