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

final class ConfigMenu extends BaseComponent
{
    /** @var array */
    protected $menu;

    public function __construct(array $menu)
    {
        $this->menu = $menu;
    }

    protected function beforeRender() : void
    {
        $this->template->menu = $this->menu;
    }
}
