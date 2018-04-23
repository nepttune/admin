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
    public static function prependDefault(RouteList $router, string $base)
    {
        $router->prepend(new Route($base . 'user/<action>[/<id>]', [
            'locale' => [Route::PATTERN => '[a-z]{2}'],
            'presenter' => 'User',
            'action' => 'default'
        ]));

        $router->prepend(new Route($base . 'sign/<action>', [
            'locale' => [Route::PATTERN => '[a-z]{2}'],
            'presenter' => 'Sign',
            'action' => 'default',
            'id' => [Route::PATTERN => '\d+']
        ]));
    }

    public static function createSubdomainRouter(string $module = 'admin') : RouteList
    {
        $router = parent::createSubdomainRouter();

        static::prependDefault($router, "//{$module}.%domain%/[<locale>/]");

        return $router;
    }

    public static function createStandardRouter(string $module = 'admin') : RouteList
    {
        $router = parent::createStandardRouter();

        $router->prepend(new Route("/[<locale>/]{$module}/<presenter>/<action>[/<id>]", [
            'locale' => [Route::PATTERN => '[a-z]{2}'],
            'presenter' => 'Default',
            'action' => 'default',
            'id' => [Route::PATTERN => '\d+']
        ]));

        static::prependDefault($router, "/[<locale>/]{$module}/");

        return $router;
    }
}
