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

namespace Nepttune\Model;

final class Authorizator
{
    use \Nette\SmartObject;

    /** @var  UserAccessModel */
    private $userAccessModel;

    /** @var \Nette\Security\User */
    private $user;

    public function __construct(UserAccessModel $userAccessModel, \Nette\Security\User $user)
    {
        $this->userAccessModel = $userAccessModel;
        $this->user = $user;
    }

    public function isAllowed($resource, $privilege = null) : bool
    {
        if ($this->user->isInRole('root'))
        {
            return true;
        }

        return $this->userAccessModel->findByArray([
            'user_id' => $this->user->getId(),
            'resource' => $resource,
            'privilege' => $privilege
        ])->count() > 0;
    }
}

