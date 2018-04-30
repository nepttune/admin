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

    /** @var \Nette\DI\Container */
    private $context;

    /** @var \Nette\Caching\Cache */
    private $cache;

    /** @var \Nepttune\Model\UserAccessModel */
    protected $userAccessModel;

    /** @var array */
    protected $privileges = [];

    public function __construct(
        \Nepttune\Model\UserModel $userModel,
        \Nepttune\Model\UserAccessModel $userAccessModel,
        \Nette\DI\Container $context,
        \Nette\Caching\IStorage $storage)
    {
        parent::__construct();
        
        $this->repository = $userModel;
        $this->userAccessModel = $userAccessModel;
        $this->context = $context;
        $this->cache = new \Nette\Caching\Cache($storage, 'Nepttune.Authorizator');

        $this->privileges = $this->getPrivileges();
    }

    public function setDefaults(int $rowId) : void
    {
        $this->rowId = $rowId;
        $data = $this->repository->findRow($rowId)->fetch()->toArray();

        $access = [];
        foreach($this->userAccessModel->findBy('user_id', $rowId) as $row)
        {
            $access[static::formatInput($row->resource, $row->privilege)] = true;
        }

        $data['access'] = $access;
        $this['form']->setDefaults($data);
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

        $access = $form->addContainer('access');
        foreach ($this->privileges as $resource => $privileges)
        {
            $base = $access->addCheckbox($resource, "access.{$resource}");

            if (empty($privileges))
            {
                continue;
            }

            $condition = $base->addCondition($form::FILLED, true);
            foreach ($privileges as $privilege)
            {
                $access->addCheckbox($privilege, "access.{$privilege}")
                    ->setOption('id', $privilege);
                $condition->toggle($privilege);
            }
        }

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

            $insert = [];
            foreach ($access as $name => $value)
            {
                $temp = static::formatResource($name);

                $insert[] = [
                    'user_id' => $row->id,
                    'resource' => $temp[0],
                    'privilege' => $temp[1]
                ];
            }

            $this->userAccessModel->delete(['user_id' => $row->id]);
            $this->userAccessModel->insertMany($insert);
        });

        $this->getPresenter()->flashMessage($this->translator->translate('global.flash.save_success'), 'success');
        $this->getPresenter()->redirect(static::REDIRECT);
    }

    protected function getPrivileges() : array
    {
        $cacheName = 'restricted_privileges';
        $return = $this->cache->load($cacheName);

        if ($return)
        {
            return $return;
        }

        $return = [];
        foreach ($this->context->findByType(\Nepttune\TI\IRestricted::class) as $name)
        {
            /** @var \Nepttune\TI\IRestricted $presenter */
            $presenter = $this->context->getService($name);

            foreach ($presenter->getRestricted() as $resource => $privileges)
            {
                $temp = [];
                foreach ($privileges as $privilage)
                {
                    $temp[] = static::formatInput($resource, $privilage);
                }
                $return[static::formatInput($resource)] = $temp;
            }
        }

        $this->cache->save($cacheName, $return);

        return $return;
    }

    protected static function formatInput(string $resource, string $privilege = null) : string
    {
        if ($privilege)
        {
            return static::formatInput($resource) . '_' . $privilege;
        }

        return str_replace(':', '_', ltrim($resource, ':'));
    }

    protected static function formatResource(string $input) : array
    {
        $split = \explode('_', $input);
        $priv  = \count($split) === 3 ? null : \array_pop($split);

        return [
            ':' . \implode(':', $split),
            $priv
        ];
    }
}
