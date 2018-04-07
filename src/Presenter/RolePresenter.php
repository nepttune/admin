<?php

namespace App\Presenter;

final class RolePresenter extends \Nepttune\Presenter\BaseAuthPresenter
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

    protected function createComponentRoleForm()
    {
        return $this->iRoleFormFactory->create();
    }

    protected function createComponentRoleList()
    {
        return $this->iRoleListFactory->create();
    }
}
