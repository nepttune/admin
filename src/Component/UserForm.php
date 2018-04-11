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

final class UserForm extends BaseFormComponent
{
    const REDIRECT = ':default';
    const SAVE_NEXT = true;

    public function __construct(\Nepttune\Model\UserModel $userModel)
    {
        $this->repository = $userModel;
    }

    protected function modifyForm(Form $form) : Form
    {
        $form->addText('username', 'admin.username')
            ->addRule([$this, static::VALIDATOR_UNIQUE], static::VALIDATOR_UNIQUE_MSG)
            ->setRequired();
        $form->addPassword('password', 'admin.password')
            ->setRequired();
        $form->addPassword('password2', 'admin.password_again')
            ->setRequired()
            ->addCondition($form::EQUAL, $form['password']);

        return $form;
    }

    public function formSuccess(\Nette\Application\UI\Form $form, \stdClass $values) : void
    {
        unset($values->password2);
        $values->registered = new \Nette\DateTime();
        $values->password = \Nette\Security\Passwords::hash($values->password);

        parent::formSuccess($form, $values);
    }
}
