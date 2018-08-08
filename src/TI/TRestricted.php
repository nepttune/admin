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
    protected $authorizator;

    /** @var \Nette\Caching\Cache */
    protected $cache;

    public function injectRestricted(
        \Nette\DI\Container $container,
        \Nette\Caching\IStorage $storage) : void
    {
        $this->authorizator = $container->getService('authorizator');
        $this->cache = new \Nette\Caching\Cache($storage, 'Nepttune.Authorizator');
    }

    public function getRestricted() : array
    {
        $cacheName = 'restrictedActions_' . ($this->getName());
        $return = $this->cache->load($cacheName);

        if ($return !== null)
        {
            return $return;
        }

        $return = static::getRestrictedStatic();

        $this->cache->save($cacheName, $return);

        return $return;
    }
    
    public static function getRestrictedStatic() : array
    {
        $return = [];

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

            $regex = '/App\\\\([A-Za-z]*)Module\\\\Presenter\\\\([A-Za-z]*)Presenter/';
            $matches = [];
            \preg_match($regex, $reflection->getName(), $matches);

            if (\count($matches) < 3)
            {
                continue;
            }

            $privileges = [];

            if ($method->hasAnnotation('privilege'))
            {
                $refl = \Nette\Reflection\Method::from($reflection->getName(), $method->getName());
                foreach ($refl->getAnnotations()['privilege'] as $privilege)
                {
                    $privileges[] = $privilege;
                }
            }

            $resource = ":{$matches[1]}:{$matches[2]}:" . lcfirst(substr($method->getName(), 6));
            $return[$resource] = $privileges;
        }
        
        return $return;
    }
}
