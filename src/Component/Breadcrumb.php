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
    public function render() : void
    {
        $module = $this->getPresenter()->getModule();
        $presenter = $this->getPresenter()->getNameWM();
        $action = $this->getPresenter()->getAction();

        $breadcrumbs = [];

        if (class_exists('\App\AppModule\Presenter\DefaultPresenter'))
        {
            $breadcrumbs[':App:Default:default'] = 'Home';
        }

        if ($module !== 'App')
        {
            $breadcrumbs['Default:default'] = $module;
        }

        if ($presenter !== 'Default')
        {
            $breadcrumbs[':default'] = $presenter;
        }

        if ($action !== 'default')
        {
            $breadcrumbs['X'] = ucfirst($action);
        }

        $this->template->breadcrumbs = $breadcrumbs;

        parent::render();
    }
}
