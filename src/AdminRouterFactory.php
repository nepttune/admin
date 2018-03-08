<?php

namespace Nepttune;

use Nette\Application\Routers\RouteList,
    Nette\Application\Routers\Route;

class AdminRouterFactory extends RouterFactory
{
    /**
     * @return \Nette\Application\IRouter
     */
    public static function createStandardRouter()
    {
        $router = parent::createStandardRouter();

        $router->prepend(new Route('/[<locale [a-z]{2}>/]admin/<presenter>/<action>[/<id>]', [
            'module' => 'Admin',
            'presenter' => 'Default',
            'action' => 'default',
            'id' => [
                Route::PATTERN => '\d+'
            ]
        ]));

        return $router;
    }
}
