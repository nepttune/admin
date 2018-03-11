<?php

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
