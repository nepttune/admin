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

use \Nette\Application\UI\Form;

final class RoleForm extends BaseFormComponent
{
    const REDIRECT = ':edit';
    const REDIRECT_ID = true;

    const SAVE_NEXT = true;
    const SAVE_LIST = true;

    public function __construct(\Nepttune\Model\RoleModel $roleModel)
    {
        $this->repository = $roleModel;
    }

    protected function modifyForm(Form $form) : Form
    {
        $form->addText('name', 'global.name')
            ->setRequired();

        return $form;
    }
}
