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

namespace Nepttune\TI;

trait TAccessForm
{
    /** @var \Nepttune\Model\RoleAccessModel */
    protected $accessModel;

    /** @var \Nette\Caching\Cache */
    private $cache;

    /** @var \Nette\DI\Container */
    private $context;

    /** @var \Nepttune\Model\Authorizator */
    protected $authorizator;

    /** @var array */
    protected $privileges = [];
    
    /** @var string */
    protected $primaryRow;

    public function injectAccessForm(
        \Nette\DI\Container $context,
        \Nette\Caching\IStorage $storage,
        \Nepttune\Model\Authorizator $authorizator) : void
    {
        $this->cache = new \Nette\Caching\Cache($storage, 'Nepttune.UserForm');
        $this->context = $context;
        $this->authorizator = $authorizator;
    }

    public function attached($presenter) : void
    {
        $this->privileges = $this->getPrivileges();
        $this->primaryRow = $this instanceof \Nepttune\Component\UserForm ? 'user_id' : 'role_id';
    }

    public function setDefaults(int $rowId) : void
    {
        $this->rowId = $rowId;
        $data = $this->repository->findRow($rowId)->fetch()->toArray();

        $access = [];
        foreach($this->accessModel->findBy($this->primaryRow, $rowId) as $row)
        {
            $access[static::formatInput($row->resource, $row->privilege)] = true;
        }

        $data['access'] = $access;
        $this['form']->setDefaults($data);
    }

    public function addCheckboxes(\Nette\Application\UI\Form $form) : \Nette\Application\UI\Form
    {
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

    protected function getPrivileges() : array
    {
        $cacheName = 'restricted_privileges_' . ($this->authorizator->isRoot() ? 'root' : $this->authorizator->getUserId());
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
                if (!$this->authorizator->isAllowed($resource))
                {
                    continue;
                }

                $temp = [];
                foreach ($privileges as $privilege)
                {
                    if (!$this->authorizator->isAllowed($resource, $privilege))
                    {
                        continue;
                    }

                    $temp[] = static::formatInput($resource, $privilege);
                }
                $return[static::formatInput($resource)] = $temp;
            }
        }

        $this->cache->save($cacheName, $return);

        return $return;
    }

    public static function formatInput(string $resource, string $privilege = null) : string
    {
        if ($privilege)
        {
            return static::formatInput($resource) . '_' . $privilege;
        }

        return \str_replace(':', '_', \ltrim($resource, ':'));
    }

    public static function formatResource(string $input) : array
    {
        $split = \explode('_', $input);
        $priv  = \count($split) === 3 ? null : \array_pop($split);

        return [
            ':' . \implode(':', $split),
            $priv
        ];
    }

    protected static function createInsertArray(int $id, array $access) : array
    {
        $insert = [];
        foreach ($access as $name => $value)
        {
            $temp = static::formatResource($name);

            $insert[] = [
                $this->primaryRow => $id,
                'resource' => $temp[0],
                'privilege' => $temp[1]
            ];
        }

        return $insert;
    }
}
