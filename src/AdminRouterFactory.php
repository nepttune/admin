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
    const ADMIN_MODULE = 'admin';

    protected static function addAdminRoutes(RouteList $router, string $base) : RouteList
    {
        $router[] = new Route($base . 'user/<action>[/<id>]', [
            'locale' => [Route::PATTERN => '[a-z]{2}'],
            'presenter' => 'User',
            'action' => 'default',
            'id' => [Route::PATTERN => '\d+']
        ]);

        $router[] = new Route($base . 'sign/<action>', [
            'locale' => [Route::PATTERN => '[a-z]{2}'],
            'presenter' => 'Sign',
            'action' => 'default'
        ]);

        return $router;
    }

    public static function createSubdomainRouter() : RouteList
    {
        $base = '//' . lcfirst(static::ADMIN_MODULE) . '.%domain%/[<locale>/]';

        $router = static::createRouteList();
        $router = static::addAdminRoutes($router, $base);
        $router = static::addSubdomainRoutes($router);

        return $router;
    }

    public static function createStandardRouter() : RouteList
    {
        $base = '/[<locale>/]' . lcfirst(static::ADMIN_MODULE) . '/';

        $router = static::createRouteList();
        $router = static::addAdminRoutes($router, $base);
        $router[] = new Route($base . '<presenter>/<action>[/<id>]', [
            'locale' => [Route::PATTERN => '[a-z]{2}'],
            'module' => static::ADMIN_MODULE,
            'presenter' => 'Default',
            'action' => 'default',
            'id' => [Route::PATTERN => '\d+']
        ]);
        $router = static::addStandardRoutes($router);

        return $router;
    }
}
