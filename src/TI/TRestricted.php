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

    public function injectRestricted(\Nepttune\Model\Authorizator $authorizator) : void
    {
        $this->authorizator = $authorizator;
    }

    public function isAllowed() : bool
    {
        /** Action is not restricted */
        if (!static::isRestricted($this->getAction()))
        {
            return true;
        }

        return $this->authorizator->isAllowed($this->getAction(true));
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

            $regex = '/App\\\\([A-Z][a-z]*)Module\\\\Presenter\\\\([A-Z][a-z]*)Presenter/';
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
