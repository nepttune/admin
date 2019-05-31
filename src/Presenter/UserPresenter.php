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

namespace Nepttune\Presenter;

abstract class UserPresenter extends BaseAuthPresenter
{
    /**
     * @inject
     * @var  \Nepttune\Component\IUserFormFactory
     */
    public $iUserFormFactory;

    /**
     * @inject
     * @var  \Nepttune\Component\IUserListFactory
     */
    public $iUserListFactory;

    protected function createComponentUserForm() : \Nepttune\Component\UserForm
    {
        $control = $this->iUserFormFactory->create();
        $control->saveCallback = function() {
            $this->redirect(':default');
        };
        
        return $control;
    }

    protected function createComponentUserList() : \Nepttune\Component\UserList
    {
        return $this->iUserListFactory->create();
    }
}
