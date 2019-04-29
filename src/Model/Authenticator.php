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

use Nette\Security as NS;

class Authenticator implements NS\IAuthenticator
{
    use \Nette\SmartObject;

    /** @var UserModel */
    private $userModel;

    /** @var \Nette\Security\Passwords */
    protected $passwords;

    public function __construct(
        UserModel $userModel,
        \Nette\Security\Passwords $passwords
    )
    {
        $this->userModel = $userModel;
        $this->passwords = $passwords;
    }
    
    public function authenticate(array $credentials) : NS\IIdentity
    {
        list($username, $password) = $credentials;
        $row = $this->userModel->findAll()
            ->where('username', $username)
            ->where('active', 1)
            ->fetch();

        if (!$row instanceof \Nette\Database\Table\ActiveRow)
        {
            throw new NS\AuthenticationException('admin.error.user');
        }

        if ($this->passwords->verify($password, $row->password))
        {
            $data = $row->toArray();
            unset($data['password']);
            return new \Nette\Security\Identity($row->id, $row->root ? ['root'] : [], $data);
        }

        throw new NS\AuthenticationException('admin.error.password');
    }
}
