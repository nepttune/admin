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

class RoleForm extends BaseFormComponent implements \Nepttune\TI\IAccessForm
{
    use \Nepttune\TI\TAccessForm;

    const REDIRECT = ':default';

    public function __construct(
        \Nepttune\Model\RoleModel $roleModel,
        \Nepttune\Model\RoleAccessModel $roleAccessModel)
    {
        parent::__construct();

        $this->repository = $roleModel;
        $this->accessModel = $roleAccessModel;
    }

    protected function modifyForm(Form $form) : Form
    {
        $form->addText('name', 'list.column.name')
            ->setRequired();
        $form->addTextArea('description', 'list.column.description');

        $form = $this->addCheckboxes($form);

        return $form;
    }

    public function formSuccess(\Nette\Application\UI\Form $form, \stdClass $values) : void
    {
        $access = \array_filter((array) $values->access, function ($value) {return $value === true;});
        unset($values->access);

        if ($this->rowId)
        {
            $values->id = $this->rowId;
        }

        $this->accessModel->transaction(function() use ($values, $access)
        {
            $row = $this->repository->save((array) $values);
            $this->accessModel->delete(['role_id' => $row->id]);
            $this->accessModel->insertMany(static::createInsertArray($row->id, $access));
        });

        $this->getPresenter()->flashMessage($this->translator->translate('global.flash.save_success'), 'success');
        $this->getPresenter()->redirect(static::REDIRECT);
    }
}
