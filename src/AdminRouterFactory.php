<?php

namespace Nepttune;

use Nette\Application\Routers\RouteList,
    Nette\Application\Routers\Route;

final class AdminRouterFactory extends RouterFactory
{
    /**
     * @return \Nette\Application\IRouter
     */
    public static function createSubdomainRouter()
    {
        $router = parent::createSubdomainRouter();

        $router->prepend(new Route('//admin.%domain%/[<locale [a-z]{2}>/]<presenter>/<action>[/<id>]', [
            'module' => 'Nepttune',
            'presenter' => [
                Route::PATTERN => 'user|role|sign'
            ],
            'action' => 'default',
            'id' => [
                Route::PATTERN => '\d+'
            ]
        ]));

        return $router;
    }

    /**
     * @return \Nette\Application\IRouter
     */
    public static function createStandardRouter()
    {
        $router = parent::createStandardRouter();

        $router->prepend(new Route('/admin/<presenter>/<action>[/<id>]', [
            'module' => 'Nepttune',
            'presenter' => [
                Route::PATTERN => 'user|role|sign'
            ],
            'action' => 'default',
            'id' => [
                Route::PATTERN => '\d+'
            ]
        ]));

        return $router;
    }
}
