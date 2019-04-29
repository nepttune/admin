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

namespace Nepttune\TI;

interface IAccessForm
{
    public function decorateAccessForm(
        \Nette\DI\Container $context,
        \Nette\Caching\IStorage $storage) : void;

    public function setDefaults(int $rowId) : void;

    public function addCheckboxes(\Nette\Application\UI\Form $form) : \Nette\Application\UI\Form;

    public static function formatInput(string $resource, string $privilege = null) : string;

    public static function formatResource(string $input) : array;
}
