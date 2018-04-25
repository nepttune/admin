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

    public function render() : void
    {
        $module = $this->getPresenter()->getModule();
        $presenter = $this->getPresenter()->getNameWM();
        $action = $this->getPresenter()->getAction();

        $breadcrumbs = [];

        if (class_exists("\App\AdminModule\Presenter\DefaultPresenter"))
        {
            $breadcrumbs[":Admin:Default:default"] = 'home';
        }

        if ($module !== 'Admin')
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
