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

abstract class RolePresenter extends BaseAuthPresenter
{
    /**
     * @inject
     * @var  \Nepttune\Component\IRoleFormFactory
     */
    public $iRoleFormFactory;
    
    /**
     * @inject
     * @var  \Nepttune\Component\IRoleListFactory
     */
    public $iRoleListFactory;
    
    protected function createComponentRoleForm() : \Nepttune\Component\RoleForm
    {
        return $this->iRoleFormFactory->create();
    }
    
    protected function createComponentRoleList() : \Nepttune\Component\RoleList
    {
        return $this->iRoleListFactory->create();
    }
}
