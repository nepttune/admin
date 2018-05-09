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

class UserForm extends BaseFormComponent implements \Nepttune\TI\IAccessForm
{
    use \Nepttune\TI\TAccessForm;

    const REDIRECT = ':default';
    const TEMPLATE_PATH = __DIR__ . '/UserForm.latte';

    /** @var \Nepttune\Model\RoleModel */
    protected $roleModel;

    public function __construct(
        \Nepttune\Model\UserModel $userModel,
        \Nepttune\Model\UserAccessModel $userAccessModel,
        \Nepttune\Model\RoleModel $roleModel)
    {
        parent::__construct();

        $this->repository = $userModel;
        $this->accessModel = $userAccessModel;
        $this->roleModel = $roleModel;
    }

    public function render() : void
    {
        $this->template->roles = $this->roleModel->findActive();

        parent::render();
    }

    protected function modifyForm(Form $form) : Form
    {
        $form->addText('username', 'admin.username')
            ->addRule([$this, static::VALIDATOR_UNIQUE], static::VALIDATOR_UNIQUE_MSG)
            ->setRequired();
        $form->addPassword('password', 'admin.password');
        $form->addPassword('password2', 'admin.password_again')
            ->addCondition($form::EQUAL, $form['password']);

        if (!$this->rowId)
        {
            $form['password']->setRequired();
            $form['password2']->setRequired();
        }

        $form->addSelect('role', 'Přednastavená role', $this->roleModel->findActive()->fetchPairs('id', 'name'))
            ->setPrompt('Vyberte roli')
            ->setOmitted();

        $form = $this->addCheckboxes($form);

        return $form;
    }

    public function formSuccess(\Nette\Application\UI\Form $form, \stdClass $values) : void
    {
        $access = \array_filter((array) $values->access, function ($value) {return $value === true;});
        unset($values->password2, $values->access);

        if (!$values->password)
        {
            unset($values->password);
        }
        if ($this->rowId)
        {
            $values->id = $this->rowId;
        }
        $values->registered = new \Nette\Utils\DateTime();
        $values->password = \Nette\Security\Passwords::hash($values->password);

        $this->userAccessModel->transaction(function() use ($values, $access)
        {
            $row = $this->repository->save((array) $values);
            $this->userAccessModel->delete(['user_id' => $row->id]);
            $this->userAccessModel->insertMany(static::createInsertArray($row->id, $access));
        });

        $this->getPresenter()->flashMessage($this->translator->translate('global.flash.save_success'), 'success');
        $this->getPresenter()->redirect(static::REDIRECT);
    }
}
