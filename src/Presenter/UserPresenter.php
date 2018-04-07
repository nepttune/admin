<?php

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
