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

namespace App\Presenter;

final class UserPresenter extends \Nepttune\Presenter\BaseAuthPresenter
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

    protected function createComponentUserForm()
    {
        return $this->iUserFormFactory->create();
    }

    protected function createComponentUserList()
    {
        return $this->iUserListFactory->create();
    }
}
