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

namespace Nepttune;

use Nette\Application\Routers\RouteList,
    Nette\Application\Routers\Route;

class AdminRouterFactory extends RouterFactory
{
    public function createStandardRouter(string $defaultModule = null) : RouteList
    {
        $router = static::createRouteList();
        $router[] = new Route('/[<locale>/]admin/<presenter>/<action>[/<id>]', [
            'locale' => [Route::PATTERN => '[a-z]{2}'],
            'module' => 'Admin',
            'presenter' => 'Default',
            'action' => 'default',
            'id' => $this->getIdConfig()
        ]);
        $router = static::addStandardRoutes($router, $defaultModule);

        return $router;
    }
}
