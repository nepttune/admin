<?php

namespace Nepttune\Component;

interface IConfigMenuFactory
{
    /** @return ConfigMenu */
    public function create() : ConfigMenu;
}
