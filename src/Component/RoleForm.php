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

    public function __construct(
        \Nepttune\Model\RoleModel $roleModel,
        \Nepttune\Model\RoleAccessModel $roleAccessModel)
    {
        $this->repository = $roleModel;
        $this->roleAccessModel = $roleAccessModel;
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
        $access = \array_filter((array) $values->access, static function ($value) {return $value === true;});
        unset($values->access);

        $rowId = $this->roleAccessModel->transaction(function() use ($values, $access)
        {
            $rowId = $this->repository->upsert($this->rowId, (array) $values);
            $this->roleAccessModel->deleteByArray(['role_id' => $rowId]);
            $this->roleAccessModel->insertMany(static::createInsertArray($rowId, $access));

            return $rowId;
        });

        if (\is_callable($this->saveCallback)) {
            \call_user_func($this->saveCallback, $form, $values, $rowId);
        }
    }
}
