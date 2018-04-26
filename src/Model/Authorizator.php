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

    public function __construct(UserAccessModel $userAccessModel)
    {
        $this->userAccessModel = $userAccessModel;
    }

    public function isAllowed($userId, $resource) : bool
    {
        return $this->userAccessModel->findByArray([
            'user_id' => $userId,
            'resource' => $resource
        ])->count() > 0;
    }
}
