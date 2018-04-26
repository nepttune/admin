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
    protected $pages = [];

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

        $this->pages = $this->getPages();
    }

    public function setDefaults(int $rowId) : void
    {
        $this->rowId = $rowId;
        $data = $this->repository->findRow($rowId)->fetch()->toArray();

        $roles = [];
        foreach($this->userAccessModel->findBy('user_id', $rowId) as $row)
        {
            $roles[static::formatInput($row->resource)] = true;
        }

        $data['roles'] = $roles;
        $this['form']->setDefaults($data);
    }

    protected function modifyForm(Form $form) : Form
    {
        $form->addText('username', 'admin.username')
            ->addRule([$this, static::VALIDATOR_UNIQUE], static::VALIDATOR_UNIQUE_MSG)
            ->setRequired();
        $form->addPassword('password', 'admin.password')
            ->setRequired();
        $form->addPassword('password2', 'admin.password_again')
            ->setRequired()
            ->addCondition($form::EQUAL, $form['password']);

        $roles = $form->addContainer('roles');
        foreach ($this->pages as $option => $resource)
        {
            $roles->addCheckbox($option, 'access.' . $option);
        }

        return $form;
    }

    public function formSuccess(\Nette\Application\UI\Form $form, \stdClass $values) : void
    {
        if ($this->rowId)
        {
            $values->id = $this->rowId;
        }

        $roles = \array_filter((array) $values->roles, function ($value) {return $value === true;});

        unset($values->password2, $values->roles);
        $values->registered = new \Nette\Utils\DateTime();
        $values->password = \Nette\Security\Passwords::hash($values->password);

        $this->userAccessModel->transaction(function() use ($values, $roles)
        {
            $row = $this->repository->save((array) $values);

            $insert = [];
            foreach ($roles as $name => $value)
            {
                $insert[] = ['user_id' => $row->id, 'resource' => $this->pages[$name]];
            }

            $this->userAccessModel->delete(['user_id' => $row->id]);
            $this->userAccessModel->insertMany($insert);
        });

        $this->getPresenter()->flashMessage($this->translator->translate('global.flash.save_success'), 'success');
        $this->getPresenter()->redirect(static::REDIRECT);
    }

    protected function getPages() : array
    {
        $cacheName = 'restricted_pages';
        $pages = $this->cache->load($cacheName);

        if ($pages)
        {
            return $pages;
        }

        $pages = [];
        foreach ($this->context->findByType(\Nepttune\TI\IRestricted::class) as $name)
        {
            /** @var \Nepttune\TI\ISitemap $presenter */
            $presenter = $this->context->getService($name);

            foreach ($presenter->getRestricted() as $resource)
            {
                $pages[static::formatInput($resource)] = $resource;
            }
        }

        $this->cache->save($cacheName, $pages);
        return $pages;
    }

    protected static function formatInput(string $resource)
    {
        return str_replace(':', '_', ltrim($resource, ':'));
    }
}
