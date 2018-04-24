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

namespace Nepttune\Component;

final class Breadcrumb extends BaseComponent
{
    use \Nepttune\TI\TTranslator;

    /** @var string */
    protected $adminModule;

    public function __construct(\Nepttune\AdminRouterFactory $routerFactory)
    {
        parent::__construct();
        
        $this->adminModule = ucfirst($routerFactory::ADMIN_MODULE);
    }

    public function render() : void
    {
        $module = $this->getPresenter()->getModule() ?: $this->adminModule;
        $presenter = $this->getPresenter()->getNameWM();
        $action = $this->getPresenter()->getAction();

        $breadcrumbs = [];

        if (class_exists("\App\\{$this->adminModule}Module\Presenter\DefaultPresenter"))
        {
            $breadcrumbs[":{$this->adminModule}:Default:default"] = 'home';
        }

        if ($module !== $this->adminModule)
        {
            $breadcrumbs['Default:default'] = lcfirst($module);
        }

        if ($presenter !== 'Default')
        {
            $breadcrumbs[':default'] = lcfirst($presenter);
        }

        if ($action !== 'default')
        {
            $breadcrumbs['X'] = lcfirst($action);
        }

        $this->template->breadcrumbs = $breadcrumbs;

        parent::render();
    }
}
